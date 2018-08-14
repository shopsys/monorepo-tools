<?php

namespace Shopsys\FrameworkBundle\Form\Admin\Product;

use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Form\Admin\Product\Parameter\ProductParameterValueFormType;
use Shopsys\FrameworkBundle\Form\Constraints\UniqueProductParameters;
use Shopsys\FrameworkBundle\Form\DisplayOnlyType;
use Shopsys\FrameworkBundle\Form\GroupType;
use Shopsys\FrameworkBundle\Form\ImageUploadType;
use Shopsys\FrameworkBundle\Form\ProductParameterValueType;
use Shopsys\FrameworkBundle\Form\ProductsType;
use Shopsys\FrameworkBundle\Form\Transformers\ProductParameterValueToProductParameterValuesLocalizedTransformer;
use Shopsys\FrameworkBundle\Form\Transformers\RemoveDuplicatesFromArrayTransformer;
use Shopsys\FrameworkBundle\Form\ValidationGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductEditFormType extends AbstractType
{
    const CSRF_TOKEN_ID = 'product_edit_type';
    const VALIDATION_GROUP_MANUAL_PRICE_CALCULATION = 'manualPriceCalculation';

    /**
     * @var \Shopsys\FrameworkBundle\Form\Transformers\RemoveDuplicatesFromArrayTransformer
     */
    private $removeDuplicatesTransformer;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade
     */
    private $pricingGroupFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    private $pluginDataFormExtensionFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Form\Transformers\ProductParameterValueToProductParameterValuesLocalizedTransformer
     */
    private $productParameterValueToProductParameterValuesLocalizedTransformer;

    public function __construct(
        RemoveDuplicatesFromArrayTransformer $removeDuplicatesTransformer,
        PricingGroupFacade $pricingGroupFacade,
        PluginCrudExtensionFacade $pluginDataFormExtensionFacade,
        ProductParameterValueToProductParameterValuesLocalizedTransformer $productParameterValueToProductParameterValuesLocalizedTransformer
    ) {
        $this->removeDuplicatesTransformer = $removeDuplicatesTransformer;
        $this->pricingGroupFacade = $pricingGroupFacade;
        $this->pluginDataFormExtensionFacade = $pluginDataFormExtensionFacade;
        $this->productParameterValueToProductParameterValuesLocalizedTransformer = $productParameterValueToProductParameterValuesLocalizedTransformer;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $editedProduct = $options['product'];
        /* @var $editedProduct \Shopsys\FrameworkBundle\Model\Product\Product */

        $productDataGroup = $builder->create('productData', ProductFormType::class, [
            'product' => $editedProduct,
            'inherit_data' => true,
            'render_form_row' => false,
        ]);

        // moved out of the productData to be rendered on the top of the form
        $nameGroup = $productDataGroup->get('name');
        $productDataGroup->remove('name');
        $builder->add($nameGroup);

        if ($editedProduct !== null && $editedProduct->isMainVariant()) {
            $builder->add('variants', ProductsType::class, [
                'required' => false,
                'main_product' => $editedProduct,
                'allow_main_variants' => false,
                'allow_variants' => false,
                'label_button_add' => t('Add variant'),
                'label' => t('Variants'),
                'top_info_title' => t('Product is main variant.'),
                'attr' => [
                    'class' => 'wrap-border',
                ],
            ]);
        }

        $builder->add($productDataGroup);

        // seo group will be rendered after the parameters group in the form
        $seoGroup = $builder->get('productData')->get('seoGroup');
        $builder->get('productData')->remove('seoGroup');

        $builderParametersGroup = $builder->create('parametersGroup', GroupType::class, [
            'label' => t('Parameters'),
            'is_group_container_to_render_as_the_last_one' => true,
        ]);

        $builderParametersGroup
            ->add($builder->create('parameters', ProductParameterValueType::class, [
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
                'render_form_row' => false,
            ])
                ->addViewTransformer($this->productParameterValueToProductParameterValuesLocalizedTransformer));

        $builder->add($builderParametersGroup);

        $builder->add($seoGroup);

        $pricesGroup = $builder->get('productData')->get('pricesGroup');

        $productCalculatedPricesGroup = $pricesGroup->get('productCalculatedPricesGroup');

        $productCalculatedPricesGroup
            ->add('manualInputPricesByPricingGroupId', FormType::class, [
                'compound' => true,
                'render_form_row' => false,
                'disabled' => $editedProduct !== null && $editedProduct->isMainVariant(),
            ]);

        $builderImageGroup = $builder->create('imageGroup', GroupType::class, [
            'label' => t('Images'),
        ]);
        $builderImageGroup
            ->add('images', ImageUploadType::class, [
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
                'entity' => $options['product'],
                'info_text' => t('You can upload following formats: PNG, JPG, GIF'),
                'label' => t('Images'),
            ]);

        $builder->add($builderImageGroup);

        $builder->add(
            $builder
                ->create('accessories', ProductsType::class, [
                    'required' => false,
                    'main_product' => $editedProduct,
                    'sortable' => true,
                    'label' => t('Accessories'),
                    'attr' => [
                        'class' => 'wrap-border',
                    ],
                ])
                ->addViewTransformer($this->removeDuplicatesTransformer)
        );

        $builder->add('save', SubmitType::class);

        $this->pluginDataFormExtensionFacade->extendForm($builder, 'product', 'pluginData');

        $manualInputPricesByPricingGroup = $builder
            ->get('productData')
            ->get('pricesGroup')
            ->get('productCalculatedPricesGroup')
            ->get('manualInputPricesByPricingGroupId');

        foreach ($this->pricingGroupFacade->getAll() as $pricingGroup) {
            $manualInputPricesByPricingGroup
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
                    'label' => $pricingGroup->getName(),
                ]);
        }

        if ($editedProduct !== null && $editedProduct->isMainVariant()) {
            $pricesGroup->remove('vat');
            $pricesGroup->remove('priceCalculationType');
            $pricesGroup->remove('productCalculatedPricesGroup');
            $pricesGroup->add('disabledPricesOnMainVariant', DisplayOnlyType::class, [
                'mapped' => false,
                'required' => true,
                'data' => t('You can set the prices on product detail of specific variant.'),
                'attr' => [
                    'class' => 'form-input-disabled form-line--disabled position__actual font-size-13',
                ],
            ]);
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
                'data_class' => ProductData::class,
                'attr' => ['novalidate' => 'novalidate'],
                'csrf_token_id' => self::CSRF_TOKEN_ID,
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];
                    $productData = $form->getData();
                    /* @var $productData \Shopsys\FrameworkBundle\Model\Product\ProductData */

                    if ($productData->priceCalculationType === Product::PRICE_CALCULATION_TYPE_MANUAL) {
                        $validationGroups[] = self::VALIDATION_GROUP_MANUAL_PRICE_CALCULATION;
                    }

                    return $validationGroups;
                },
            ]);
    }
}
