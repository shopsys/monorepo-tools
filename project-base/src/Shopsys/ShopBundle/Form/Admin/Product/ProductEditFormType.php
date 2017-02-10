<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Component\Constraints\UniqueProductParameters;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Transformers\ImagesIdsToImagesTransformer;
use Shopsys\ShopBundle\Component\Transformers\ProductParameterValueToProductParameterValuesLocalizedTransformer;
use Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory;
use Shopsys\ShopBundle\Form\Admin\Product\ProductFormTypeFactory;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductEditFormType extends AbstractType
{
    const INTENTION = 'product_edit_type';
    const VALIDATION_GROUP_MANUAL_PRICE_CALCULATION = 'manualPriceCalculation';

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Image[]
     */
    private $images;

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory
     */
    private $productParameterValueFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Form\Admin\Product\ProductFormTypeFactory
     */
    private $productFormTypeFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[]
     */
    private $pricingGroups;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[]
     */
    private $domains;

    /**
     * @var string[]
     */
    private $metaDescriptionsIndexedByDomainId;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product|null
     */
    private $product;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var \Shopsys\ShopBundle\Component\Transformers\ImagesIdsToImagesTransformer
     */
    private $imagesIdsToImagesTransformer;

    /**
     * @param \Shopsys\ShopBundle\Component\Image\Image[] $images
     * @param \Shopsys\ShopBundle\Form\Admin\Product\Parameter\ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory
     * @param \Shopsys\ShopBundle\Form\Admin\Product\ProductFormTypeFactory
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup[] $pricingGroups
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig[] $domains
     * @param string[] $metaDescriptionsIndexedByDomainId
     * @param \Shopsys\ShopBundle\Component\Transformers\RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer
     * @param \Shopsys\ShopBundle\Model\Product\Product|null $product
     */
    public function __construct(
        array $images,
        ProductParameterValueFormTypeFactory $productParameterValueFormTypeFactory,
        ProductFormTypeFactory $productFormTypeFactory,
        array $pricingGroups,
        array $domains,
        array $metaDescriptionsIndexedByDomainId,
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        ImagesIdsToImagesTransformer $imagesIdsToImagesTransformer,
        Product $product = null
    ) {
        $this->images = $images;
        $this->productParameterValueFormTypeFactory = $productParameterValueFormTypeFactory;
        $this->productFormTypeFactory = $productFormTypeFactory;
        $this->pricingGroups = $pricingGroups;
        $this->domains = $domains;
        $this->metaDescriptionsIndexedByDomainId = $metaDescriptionsIndexedByDomainId;
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
        $this->imagesIdsToImagesTransformer = $imagesIdsToImagesTransformer;
        $this->product = $product;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'product_edit_form';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $seoTitlesOptionsByDomainId = [];
        $seoMetaDescriptionsOptionsByDomainId = [];
        foreach ($this->domains as $domainConfig) {
            $seoTitlesOptionsByDomainId[$domainConfig->getId()] = [
                'attr' => [
                    'placeholder' => $this->getTitlePlaceholder($domainConfig),
                ],
            ];
            $seoMetaDescriptionsOptionsByDomainId[$domainConfig->getId()] = [
                'attr' => [
                    'placeholder' => $this->getMetaDescriptionPlaceholder($domainConfig),
                ],
            ];
        }

        $builder
            ->add('productData', $this->productFormTypeFactory->create($this->product))
            ->add('imagesToUpload', FormType::FILE_UPLOAD, [
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
                $builder->create('imagePositions', FormType::COLLECTION, [
                    'required' => false,
                    'type' => FormType::HIDDEN,
                ])->addModelTransformer($this->imagesIdsToImagesTransformer)
            )
            ->add('imagesToDelete', FormType::CHOICE, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choice_list' => new ObjectChoiceList($this->images, 'filename', [], null, 'id'),
            ])
            ->add($builder->create('parameters', FormType::COLLECTION, [
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'type' => $this->productParameterValueFormTypeFactory->create(),
                    'constraints' => [
                        new UniqueProductParameters([
                            'message' => 'Each parameter can be used only once',
                        ]),
                    ],
                    'error_bubbling' => false,
                ])
                ->addViewTransformer(new ProductParameterValueToProductParameterValuesLocalizedTransformer())
            )
            ->add('manualInputPrices', FormType::FORM, [
                'compound' => true,
            ])
            ->add('seoTitles', FormType::MULTIDOMAIN, [
                'type' => FormType::TEXT,
                'required' => false,
                'optionsByDomainId' => $seoTitlesOptionsByDomainId,
            ])
            ->add('seoMetaDescriptions', FormType::MULTIDOMAIN, [
                'type' => FormType::TEXTAREA,
                'required' => false,
                'optionsByDomainId' => $seoMetaDescriptionsOptionsByDomainId,
            ])
            ->add('descriptions', FormType::MULTIDOMAIN, [
                'type' => FormType::WYSIWYG,
                'required' => false,
            ])
            ->add('shortDescriptions', FormType::MULTIDOMAIN, [
                'type' => FormType::TEXTAREA,
                'required' => false,
            ])
            ->add('urls', FormType::URL_LIST, [
                'route_name' => 'front_product_detail',
                'entity_id' => $this->product === null ? null : $this->product->getId(),
            ])
            ->add(
                $builder
                    ->create('accessories', FormType::PRODUCTS, [
                        'required' => false,
                        'main_product' => $this->product,
                        'sortable' => true,
                    ])
                    ->addViewTransformer($this->removeDuplicatesTransformer)
            )
            ->add('heurekaCpcValues', FormType::MULTIDOMAIN, [
                'type' => FormType::MONEY,
                'required' => false,
                'options' => [
                    'currency' => 'CZK',
                    'precision' => 2,
                    'constraints' => [
                        new Constraints\Range([
                            'min' => 0,
                            'max' => 100,
                        ]),
                    ],
                ],
            ])
            ->add('showInZboziFeed', FormType::MULTIDOMAIN, [
                'type' => FormType::YES_NO,
                'required' => false,
            ])
            ->add('zboziCpcValues', FormType::MULTIDOMAIN, [
                'type' => FormType::MONEY,
                'required' => false,
                'options' => [
                    'currency' => 'CZK',
                    'precision' => 2,
                    'constraints' => [
                        new Constraints\Range([
                            'min' => 1,
                            'max' => 500,
                        ]),
                    ],
                ],
            ])
            ->add('zboziCpcSearchValues', FormType::MULTIDOMAIN, [
                'type' => FormType::MONEY,
                'required' => false,
                'options' => [
                    'currency' => 'CZK',
                    'precision' => 2,
                    'constraints' => [
                        new Constraints\Range([
                            'min' => 1,
                            'max' => 500,
                        ]),
                    ],
                ],
            ])
            ->add('save', FormType::SUBMIT);

        foreach ($this->pricingGroups as $pricingGroup) {
            $builder->get('manualInputPrices')
                ->add($pricingGroup->getId(), FormType::MONEY, [
                    'currency' => false,
                    'precision' => 6,
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

        if ($this->product !== null && $this->product->isMainVariant()) {
            $builder->add('variants', FormType::PRODUCTS, [
                'required' => false,
                'main_product' => $this->product,
                'allow_main_variants' => false,
                'allow_variants' => false,
            ]);
        }

        if ($this->product !== null) {
            $this->disableIrrelevantFields($builder, $this->product);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductEditData::class,
            'attr' => ['novalidate' => 'novalidate'],
            'intention' => self::INTENTION,
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
     * @return string
     */
    private function getTitlePlaceholder(DomainConfig $domainConfig)
    {
        if ($this->product === null) {
            return '';
        } else {
            return $this->product->getName($domainConfig->getLocale());
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    private function getMetaDescriptionPlaceholder(DomainConfig $domainConfig)
    {
        return $this->metaDescriptionsIndexedByDomainId[$domainConfig->getId()];
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
