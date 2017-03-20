<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\ShopBundle\Component\Constraints\UniqueProductParameters;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Image\ImageFacade;
use Shopsys\ShopBundle\Component\Transformers\ImagesIdsToImagesTransformer;
use Shopsys\ShopBundle\Component\Transformers\ProductParameterValueToProductParameterValuesLocalizedTransformer;
use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormType;
use Shopsys\ShopBundle\Form\FileUploadType;
use Shopsys\ShopBundle\Form\MultidomainType;
use Shopsys\ShopBundle\Form\ProductsType;
use Shopsys\ShopBundle\Form\UrlListType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Form\YesNoType;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditData;
use Shopsys\ShopBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductEditFormType extends AbstractType
{
    const CSRF_TOKEN_ID = 'product_edit_type';
    const VALIDATION_GROUP_MANUAL_PRICE_CALCULATION = 'manualPriceCalculation';

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\ImagesIdsToImagesTransformer
     */
    private $imagesIdsToImagesTransformer;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    public function __construct(
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        ImagesIdsToImagesTransformer $imagesIdsToImagesTransformer,
        ImageFacade $imageFacade,
        PricingGroupFacade $pricingGroupFacade,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade
    ) {
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
        $this->imagesIdsToImagesTransformer = $imagesIdsToImagesTransformer;
        $this->imageFacade = $imageFacade;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editedProduct = $options['product'];
        $seoTitlesOptionsByDomainId = [];
        $seoMetaDescriptionsOptionsByDomainId = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();

            $seoTitlesOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($domainConfig, $editedProduct),
                ],
            ];
            $seoMetaDescriptionsOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->seoSettingFacade->getDescriptionMainPage($domainId),
                ],
            ];
        }

        if ($editedProduct !== null) {
            $existingImages = $this->imageFacade->getImagesByEntityIndexedById($editedProduct, null);
        } else {
            $existingImages = [];
        }

        $builder
            ->add('productData', ProductFormType::class, ['product' => $editedProduct])
            ->add('imagesToUpload', FileUploadType::class, [
                'required' => false,
                'multiple' => true,
                'file_constraints' => [
                    new Constraints\Image([
                        'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'],
                        'mimeTypesMessage' => 'Image can be only in JPG, GIF or PNG format',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Uploaded image is to large ({{ size }} {{ suffix }}). '
                            . 'Maximum size of an image is {{ limit }} {{ suffix }}.',
                    ]),
                ],
            ])
            ->add(
                $builder->create('imagePositions', CollectionType::class, [
                    'required' => false,
                    'type' => HiddenType::class,
                ])->addModelTransformer($this->imagesIdsToImagesTransformer)
            )
            ->add('imagesToDelete', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choice_list' => new ObjectChoiceList($existingImages, 'filename', [], null, 'id'),
            ])
            ->add($builder->create('parameters', CollectionType::class, [
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'type' => ProductParameterValueFormType::class,
                    'constraints' => [
                        new UniqueProductParameters([
                            'message' => 'Each parameter can be used only once',
                        ]),
                    ],
                    'error_bubbling' => false,
                ])
                ->addViewTransformer(new ProductParameterValueToProductParameterValuesLocalizedTransformer()))
            ->add('manualInputPrices', FormType::class, [
                'compound' => true,
            ])
            ->add('seoTitles', MultidomainType::class, [
                'type' => TextType::class,
                'required' => false,
                'optionsByDomainId' => $seoTitlesOptionsByDomainId,
            ])
            ->add('seoMetaDescriptions', MultidomainType::class, [
                'type' => TextareaType::class,
                'required' => false,
                'optionsByDomainId' => $seoMetaDescriptionsOptionsByDomainId,
            ])
            ->add('descriptions', MultidomainType::class, [
                'type' => CKEditorType::class,
                'required' => false,
            ])
            ->add('shortDescriptions', MultidomainType::class, [
                'type' => TextareaType::class,
                'required' => false,
            ])
            ->add('urls', UrlListType::class, [
                'route_name' => 'front_product_detail',
                'entity_id' => $editedProduct !== null ? $editedProduct->getId() : null,
            ])
            ->add(
                $builder
                    ->create('accessories', ProductsType::class, [
                        'required' => false,
                        'main_product' => $editedProduct,
                        'sortable' => true,
                    ])
                    ->addViewTransformer($this->removeDuplicatesTransformer)
            )
            ->add('heurekaCpcValues', MultidomainType::class, [
                'type' => MoneyType::class,
                'required' => false,
                'options' => [
                    'currency' => 'CZK',
                    'scale' => 2,
                    'constraints' => [
                        new Constraints\Range([
                            'min' => 0,
                            'max' => 100,
                        ]),
                    ],
                ],
            ])
            ->add('showInZboziFeed', MultidomainType::class, [
                'type' => YesNoType::class,
                'required' => false,
            ])
            ->add('zboziCpcValues', MultidomainType::class, [
                'type' => MoneyType::class,
                'required' => false,
                'options' => [
                    'currency' => 'CZK',
                    'scale' => 2,
                    'constraints' => [
                        new Constraints\Range([
                            'min' => 1,
                            'max' => 500,
                        ]),
                    ],
                ],
            ])
            ->add('zboziCpcSearchValues', MultidomainType::class, [
                'type' => MoneyType::class,
                'required' => false,
                'options' => [
                    'currency' => 'CZK',
                    'scale' => 2,
                    'constraints' => [
                        new Constraints\Range([
                            'min' => 1,
                            'max' => 500,
                        ]),
                    ],
                ],
            ])
            ->add('save', SubmitType::class);

        foreach ($this->pricingGroupFacade->getAll() as $pricingGroup) {
            $builder->get('manualInputPrices')
                ->add($pricingGroup->getId(), MoneyType::class, [
                    'currency' => false,
                    'scale' => 6,
                    'required' => true,
                    'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                    'constraints' => [
                        new Constraints\NotBlank([
                            'message' => 'Please enter price',
                            'groups' => [self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION],
                        ]),
                        new Constraints\GreaterThanOrEqual([
                            'value' => 0,
                            'message' => 'Price must be greater or equal to {{ compared_value }}',
                            'groups' => [self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION],
                        ]),
                    ],
                ]);
        }

        if ($editedProduct !== null && $editedProduct->isMainVariant()) {
            $builder->add('variants', ProductsType::class, [
                'required' => false,
                'main_product' => $editedProduct,
                'allow_main_variants' => false,
                'allow_variants' => false,
            ]);
        }

        if ($editedProduct !== null) {
            $this->disableIrrelevantFields($builder, $editedProduct);
        }
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('product')
            ->setAllowedTypes('product', [Product::class, 'null'])
            ->setDefaults([
                'data_class' => ProductEditData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'csrf_token_id' => self::CSRF_TOKEN_ID,
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];
                    $productData = $form->getData()->productData;
                    /* @var $productData \Shopsys\ShopBundle\Model\Product\ProductData */

                    if ($productData->priceCalculationType === Product::PRICE_CALCULATION_TYPE_MANUAL) {
                        $validationGroups[] = self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION;
                    }

                    return $validationGroups;
                },
            ]);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\ShopBundle\Model\Product\Product|null $product
     * @return string
     */
    private function getTitlePlaceholder(DomainConfig $domainConfig, Product $product = null)
    {
        $domainLocale = $domainConfig->getLocale();

        return $product !== null ? $product->getName($domainLocale) : '';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     */
    private function disableIrrelevantFields(FormBuilderInterface $builder, Product $product)
    {
        if ($product->isMainVariant()) {
            $builder->get('manualInputPrices')->setDisabled(true);
        }
        if ($product->isVariant()) {
            $builder->get('descriptions')->setDisabled(true);
        }
    }
}
