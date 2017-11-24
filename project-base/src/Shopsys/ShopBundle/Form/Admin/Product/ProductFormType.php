<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\FormTypesBundle\MultidomainType;
use Shopsys\FormTypesBundle\YesNoType;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\CategoriesType;
use Shopsys\ShopBundle\Form\DatePickerType;
use Shopsys\ShopBundle\Form\Locale\LocalizedType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Brand\BrandFacade;
use Shopsys\ShopBundle\Model\Product\Flag\FlagFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Model\Product\Unit\UnitFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductFormType extends AbstractType
{
    const VALIDATION_GROUP_AUTO_PRICE_CALCULATION = 'autoPriceCalculation';
    const VALIDATION_GROUP_USING_STOCK = 'usingStock';
    const VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY = 'usingStockAndAlternateAvailability';
    const VALIDATION_GROUP_NOT_USING_STOCK = 'notUsingStock';

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade
     */
    private $availabilityFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandFacade
     */
    private $brandFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\FlagFacade
     */
    private $flagFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Unit\UnitFacade
     */
    private $unitFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        VatFacade $vatFacade,
        AvailabilityFacade $availabilityFacade,
        BrandFacade $brandFacade,
        FlagFacade $flagFacade,
        UnitFacade $unitFacade,
        Domain $domain
    ) {
        $this->vatFacade = $vatFacade;
        $this->availabilityFacade = $availabilityFacade;
        $this->brandFacade = $brandFacade;
        $this->flagFacade = $flagFacade;
        $this->unitFacade = $unitFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $vats = $this->vatFacade->getAllIncludingMarkedForDeletion();

        if ($options['product'] !== null && $options['product']->isVariant()) {
            $builder->add('variantAlias', LocalizedType::class, [
                'required' => false,
                'entry_options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Variant alias cannot be longer then {{ limit }} characters']),
                    ],
                ],
            ]);
        }

        $categoriesOptionsByDomainId = [];
        foreach ($this->domain->getAllIds() as $domainId) {
            $categoriesOptionsByDomainId[$domainId] = [
                'domain_id' => $domainId,
            ];
        }

        $builder
            ->add('name', LocalizedType::class, [
                'required' => false,
                'entry_options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Product name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('hidden', YesNoType::class, ['required' => false])
            ->add('sellingDenied', YesNoType::class, [
                'required' => false,
            ])
            ->add('catnum', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Catalogue number cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('partno', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Part number cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('ean', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'EAN cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('brand', ChoiceType::class, [
                'required' => false,
                'choices' => $this->brandFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => t('-- Choose brand --'),
            ])
            ->add('usingStock', YesNoType::class, ['required' => false])
            ->add('stockQuantity', IntegerType::class, [
                'required' => true,
                'invalid_message' => 'Please enter a number',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter stock quantity',
                        'groups' => self::VALIDATION_GROUP_USING_STOCK,
                    ]),
                ],
            ])
            ->add('unit', ChoiceType::class, [
                'required' => true,
                'choices' => $this->unitFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose unit',
                    ]),
                ],
            ])
            ->add('availability', ChoiceType::class, [
                'required' => true,
                'choices' => $this->availabilityFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => t('-- Choose availability --'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose availability',
                        'groups' => self::VALIDATION_GROUP_NOT_USING_STOCK,
                    ]),
                ],
            ])
            ->add('outOfStockAction', ChoiceType::class, [
                'required' => true,
                'expanded' => false,
                'choices' => [
                    t('Set alternative availability') => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
                    t('Hide product') => Product::OUT_OF_STOCK_ACTION_HIDE,
                    t('Exclude from sale') => Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE,
                ],
                'placeholder' => t('-- Choose action --'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose action',
                        'groups' => self::VALIDATION_GROUP_USING_STOCK,
                    ]),
                ],
            ])
            ->add('outOfStockAvailability', ChoiceType::class, [
                'required' => true,
                'choices' => $this->availabilityFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'placeholder' => t('-- Choose availability --'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose availability',
                        'groups' => self::VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY,
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'currency' => false,
                'scale' => 6,
                'required' => true,
                'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter price',
                        'groups' => self::VALIDATION_GROUP_AUTO_PRICE_CALCULATION,
                    ]),
                    new Constraints\GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Price must be greater or equal to {{ compared_value }}',
                        'groups' => self::VALIDATION_GROUP_AUTO_PRICE_CALCULATION,
                    ]),
                ],
            ])
            ->add('vat', ChoiceType::class, [
                'required' => true,
                'choices' => $vats,
                'choice_label' => 'name',
                'choice_value' => 'id',
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
            ])
            ->add('sellingFrom', DatePickerType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Date(['message' => 'Enter date in DD.MM.YYYY format']),
                ],
                'invalid_message' => 'Enter date in DD.MM.YYYY format',
            ])
            ->add('sellingTo', DatePickerType::class, [
                'required' => false,
                'constraints' => [
                    new Constraints\Date(['message' => 'Enter date in DD.MM.YYYY format']),
                ],
                'invalid_message' => 'Enter date in DD.MM.YYYY format',
            ])
            ->add('flags', ChoiceType::class, [
                'required' => false,
                'choices' => $this->flagFacade->getAll(),
                'choice_label' => 'name',
                'choice_value' => 'id',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('priceCalculationType', ChoiceType::class, [
                'required' => true,
                'expanded' => true,
                'choices' => [
                    t('Automatically') => Product::PRICE_CALCULATION_TYPE_AUTO,
                    t('Manually') => Product::PRICE_CALCULATION_TYPE_MANUAL,
                ],
            ])
            ->add('orderingPriority', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter sorting priority']),
                ],
            ])
            ->add('categoriesByDomainId', MultidomainType::class, [
                'required' => false,
                'entry_type' => CategoriesType::class,
                'options_by_domain_id' => $categoriesOptionsByDomainId,
            ]);

        if ($options['product'] !== null) {
            $this->disableIrrelevantFields($builder, $options['product']);
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
                'validation_groups' => function (FormInterface $form) {
                    $validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];
                    $productData = $form->getData();
                    /* @var $productData \Shopsys\ShopBundle\Model\Product\ProductData */

                    if ($productData->usingStock) {
                        $validationGroups[] = self::VALIDATION_GROUP_USING_STOCK;
                        if ($productData->outOfStockAction === Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY) {
                            $validationGroups[] = self::VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY;
                        }
                    } else {
                        $validationGroups[] = self::VALIDATION_GROUP_NOT_USING_STOCK;
                    }

                    if ($productData->priceCalculationType === Product::PRICE_CALCULATION_TYPE_AUTO) {
                        $validationGroups[] = self::VALIDATION_GROUP_AUTO_PRICE_CALCULATION;
                    }

                    return $validationGroups;
                },
            ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     */
    private function disableIrrelevantFields(FormBuilderInterface $builder, Product $product)
    {
        $irrelevantFields = [];
        if ($product->isMainVariant()) {
            $irrelevantFields = [
                'catnum',
                'partno',
                'ean',
                'usingStock',
                'availability',
                'price',
                'vat',
                'priceCalculationType',
                'stockQuantity',
                'outOfStockAction',
                'outOfStockAvailability',
            ];
        }
        if ($product->isVariant()) {
            $irrelevantFields = [
                'categoriesByDomainId',
            ];
        }
        foreach ($irrelevantFields as $irrelevantField) {
            $builder->get($irrelevantField)->setDisabled(true);
        }
    }
}
