<?php

namespace Shopsys\ShopBundle\Form\Admin\Product;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\CategoriesType;
use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Form\ValidationGroup;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Brand\BrandFacade;
use Shopsys\ShopBundle\Model\Product\Flag\FlagFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Model\Product\Unit\UnitFacade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ProductFormType extends AbstractType
{
    const VALIDATION_GROUP_AUTO_PRICE_CALCULATION = 'autoPriceCalculation';
    const VALIDATION_GROUP_USING_STOCK = 'usingStock';
    const VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY = 'usingStockAndAlternateAvaiability';
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
            $builder->add('variantAlias', FormType::LOCALIZED, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Variant alias cannot be longer then {{ limit }} characters']),
                    ],
                ],
            ]);
        }

        $builder
            ->add('name', FormType::LOCALIZED, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Constraints\Length(['max' => 255, 'maxMessage' => 'Product name cannot be longer than {{ limit }} characters']),
                    ],
                ],
            ])
            ->add('hidden', FormType::YES_NO, ['required' => false])
            ->add('sellingDenied', FormType::YES_NO, [
                'required' => false,
            ])
            ->add('catnum', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Catalogue number cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('partno', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'Part number cannot be longer than {{ limit }} characters']),
                ],
            ])
            ->add('ean', FormType::TEXT, [
                'required' => false,
                'constraints' => [
                    new Constraints\Length(['max' => 100, 'maxMessage' => 'EAN cannot be longer then {{ limit }} characters']),
                ],
            ])
            ->add('brand', FormType::CHOICE, [
                'required' => false,
                'choice_list' => new ObjectChoiceList($this->brandFacade->getAll(), 'name', [], null, 'id'),
                'placeholder' => t('-- Choose brand --'),
            ])
            ->add('usingStock', FormType::YES_NO, ['required' => false])
            ->add('stockQuantity', FormType::INTEGER, [
                'required' => true,
                'invalid_message' => 'Please enter a number',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter stock quantity',
                        'groups' => self::VALIDATION_GROUP_USING_STOCK,
                    ]),
                ],
            ])
            ->add('unit', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->unitFacade->getAll(), 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose unit',
                    ]),
                ],
            ])
            ->add('availability', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->availabilityFacade->getAll(), 'name', [], null, 'id'),
                'placeholder' => t('-- Choose availability --'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose availability',
                        'groups' => self::VALIDATION_GROUP_NOT_USING_STOCK,
                    ]),
                ],
            ])
            ->add('outOfStockAction', FormType::CHOICE, [
                'required' => true,
                'expanded' => false,
                'choices' => [
                    Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY => t('Set alternative availability'),
                    Product::OUT_OF_STOCK_ACTION_HIDE => t('Hide product'),
                    Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE => t('Exclude from sale'),
                ],
                'placeholder' => t('-- Choose action --'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose action',
                        'groups' => self::VALIDATION_GROUP_USING_STOCK,
                    ]),
                ],
            ])
            ->add('outOfStockAvailability', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($this->availabilityFacade->getAll(), 'name', [], null, 'id'),
                'placeholder' => t('-- Choose availability --'),
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please choose availability',
                        'groups' => self::VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY,
                    ]),
                ],
            ])
            ->add('price', FormType::MONEY, [
                'currency' => false,
                'precision' => 6,
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
            ->add('vat', FormType::CHOICE, [
                'required' => true,
                'choice_list' => new ObjectChoiceList($vats, 'name', [], null, 'id'),
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
                ],
            ])
            ->add('sellingFrom', FormType::DATE_PICKER, [
                'required' => false,
                'constraints' => [
                    new Constraints\Date(['message' => 'Enter date in DD.MM.YYYY format']),
                ],
                'invalid_message' => 'Enter date in DD.MM.YYYY format',
            ])
            ->add('sellingTo', FormType::DATE_PICKER, [
                'required' => false,
                'constraints' => [
                    new Constraints\Date(['message' => 'Enter date in DD.MM.YYYY format']),
                ],
                'invalid_message' => 'Enter date in DD.MM.YYYY format',
            ])
            ->add('flags', FormType::CHOICE, [
                'required' => false,
                'choice_list' => new ObjectChoiceList($this->flagFacade->getAll(), 'name', [], null, 'id'),
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('priceCalculationType', FormType::CHOICE, [
                'required' => true,
                'expanded' => true,
                'choices' => [
                    Product::PRICE_CALCULATION_TYPE_AUTO => t('Automatically'),
                    Product::PRICE_CALCULATION_TYPE_MANUAL => t('Manually'),
                ],
            ])
            ->add('orderingPriority', FormType::INTEGER, [
                'required' => true,
                'constraints' => [
                    new Constraints\NotBlank(['message' => 'Please enter sorting priority']),
                ],
            ]);

        $builder->add('categoriesByDomainId', FormType::FORM, ['required' => false]);
        foreach ($this->domain->getAllIds() as $domainId) {
            $builder->get('categoriesByDomainId')->add($domainId, FormType::CATEGORIES, [
                'required' => false,
                CategoriesType::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID => $domainId,
            ]);
        }

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
