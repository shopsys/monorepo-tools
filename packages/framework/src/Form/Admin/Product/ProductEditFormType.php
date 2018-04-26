<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FrameworkBundle\Component\Constraints\UniqueProductParameters;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Transformers\ImagesIdsToImagesTransformer;
use Shopsys\FrameworkBundle\Component\Transformers\ProductParameterValueToProductParameterValuesLocalizedTransformer;
use Shopsys\FrameworkBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\FrameworkBundle\Form\Admin\Product\Parameter\ProductParameterValueFormType;
use Shopsys\FrameworkBundle\Form\ImageUploadType;
use Shopsys\FrameworkBundle\Form\ProductsType;
use Shopsys\FrameworkBundle\Form\UrlListType;
use Shopsys\FrameworkBundle\Form\ValidationGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductEditData;
use Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade;
use Symfony\Component\Form\AbstractType;
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
     * @var \Shopsys\FrameworkBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Transformers\ImagesIdsToImagesTransformer
     */
    private $imagesIdsToImagesTransformer;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    private $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Seo\SeoSettingFacade
     */
    private $seoSettingFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginDataFormExtensionFacade;

    public function __construct(
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        ImagesIdsToImagesTransformer $imagesIdsToImagesTransformer,
        ImageFacade $imageFacade,
        PricingGroupFacade $pricingGroupFacade,
        Domain $domain,
        SeoSettingFacade $seoSettingFacade,
        PluginCrudExtensionFacade $pluginDataFormExtensionFacade
    ) {
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
        $this->imagesIdsToImagesTransformer = $imagesIdsToImagesTransformer;
        $this->imageFacade = $imageFacade;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->domain = $domain;
        $this->seoSettingFacade = $seoSettingFacade;
        $this->pluginDataFormExtensionFacade = $pluginDataFormExtensionFacade;
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
        $seoH1OptionsByDomainId = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $domainId = $domainConfig->getId();

            $seoTitlesOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($domainConfig, $editedProduct),
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'product_edit_form_productData_name_' . $domainConfig->getLocale(),
                ],
            ];
            $seoMetaDescriptionsOptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->seoSettingFacade->getDescriptionMainPage($domainId),
                ],
            ];
            $seoH1OptionsByDomainId[$domainId] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($domainConfig, $editedProduct),
                    'class' => 'js-dynamic-placeholder',
                    'data-placeholder-source-input-id' => 'product_edit_form_productData_name_' . $domainConfig->getLocale(),
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
            ->add('imagesToUpload', ImageUploadType::class, [
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
                'info_text' => t('You can upload following formats: PNG, JPG, GIF'),
            ])
            ->add(
                $builder->create('orderedImagesById', CollectionType::class, [
                    'required' => false,
                    'entry_type' => HiddenType::class,
                ])->addModelTransformer($this->imagesIdsToImagesTransformer)
            )
            ->add('imagesToDelete', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => $existingImages,
                'choice_label' => 'filename',
                'choice_value' => 'id',
            ])
            ->add($builder->create('parameters', CollectionType::class, [
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => ProductParameterValueFormType::class,
                    'constraints' => [
                        new UniqueProductParameters([
                            'message' => 'Each parameter can be used only once',
                        ]),
                    ],
                    'error_bubbling' => false,
                ])
                ->addViewTransformer(new ProductParameterValueToProductParameterValuesLocalizedTransformer()))
            ->add('manualInputPricesByPricingGroupId', FormType::class, [
                'compound' => true,
            ])
            ->add('seoTitles', MultidomainType::class, [
                'entry_type' => TextType::class,
                'required' => false,
                'options_by_domain_id' => $seoTitlesOptionsByDomainId,
            ])
            ->add('seoMetaDescriptions', MultidomainType::class, [
                'entry_type' => TextareaType::class,
                'required' => false,
                'options_by_domain_id' => $seoMetaDescriptionsOptionsByDomainId,
            ])
            ->add('seoH1s', MultidomainType::class, [
                'entry_type' => TextType::class,
                'required' => false,
                'options_by_domain_id' => $seoH1OptionsByDomainId,
            ])
            ->add('descriptions', MultidomainType::class, [
                'entry_type' => CKEditorType::class,
                'required' => false,
            ])
            ->add('shortDescriptions', MultidomainType::class, [
                'entry_type' => TextareaType::class,
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
            ->add('save', SubmitType::class);

        $this->pluginDataFormExtensionFacade->extendForm($builder, 'product', 'pluginData');

        foreach ($this->pricingGroupFacade->getAll() as $pricingGroup) {
            $builder->get('manualInputPricesByPricingGroupId')
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
                    /* @var $productData \Shopsys\FrameworkBundle\Model\Product\ProductData */

                    if ($productData->priceCalculationType === Product::PRICE_CALCULATION_TYPE_MANUAL) {
                        $validationGroups[] = self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION;
                    }

                    return $validationGroups;
                },
            ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $product
     * @return string
     */
    private function getTitlePlaceholder(DomainConfig $domainConfig, Product $product = null)
    {
        $domainLocale = $domainConfig->getLocale();

        return $product !== null ? $product->getName($domainLocale) : '';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     */
    private function disableIrrelevantFields(FormBuilderInterface $builder, Product $product)
    {
        if ($product->isMainVariant()) {
            $builder->get('manualInputPricesByPricingGroupId')->setDisabled(true);
        }
        if ($product->isVariant()) {
            $builder->get('descriptions')->setDisabled(true);
        }
    }
}
