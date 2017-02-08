<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Form\CategoriesType;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductFormType extends AbstractType {

	const VALIDATION_GROUP_AUTO_PRICE_CALCULATION = 'autoPriceCalculation';
	const VALIDATION_GROUP_USING_STOCK = 'usingStock';
	const VALIDATION_GROUP_USING_STOCK_AND_ALTERNATE_AVAILABILITY = 'usingStockAndAlternateAvaiability';
	const VALIDATION_GROUP_NOT_USING_STOCK = 'notUsingStock';

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat[]
	 */
	private $vats;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	private $availabilities;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand[]
	 */
	private $brands;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	private $flags;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\Unit[]
	 */
	private $units;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product|null
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Config\DomainConfig[]
	 */
	private $domainConfigs;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability[] $availabilities
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand[] $brands
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flags
	 * @param \SS6\ShopBundle\Model\Product\Unit\Unit[] $units
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig[] $domainConfigs
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	public function __construct(
		array $vats,
		array $availabilities,
		array $brands,
		array $flags,
		array $units,
		array $domainConfigs,
		Product $product = null
	) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
		$this->brands = $brands;
		$this->flags = $flags;
		$this->units = $units;
		$this->domainConfigs = $domainConfigs;
		$this->product = $product;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		if ($this->product !== null && $this->product->isVariant()) {
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
				'choice_list' => new ObjectChoiceList($this->brands, 'name', [], null, 'id'),
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
				'choice_list' => new ObjectChoiceList($this->units, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Please choose unit',
					]),
				],
			])
			->add('availability', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->availabilities, 'name', [], null, 'id'),
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
				'choice_list' => new ObjectChoiceList($this->availabilities, 'name', [], null, 'id'),
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
				'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
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
				'choice_list' => new ObjectChoiceList($this->flags, 'name', [], null, 'id'),
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
		foreach ($this->domainConfigs as $domainConfig) {
			$builder->get('categoriesByDomainId')->add($domainConfig->getId(), FormType::CATEGORIES, [
				'required' => false,
				CategoriesType::OPTION_MUTED_NOT_VISIBLE_ON_DOMAIN_ID => $domainConfig->getId(),
			]);
		}

		if ($this->product !== null) {
			$this->disableIrrelevantFields($builder, $this->product);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => ProductData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = [ValidationGroup::VALIDATION_GROUP_DEFAULT];
				$productData = $form->getData();
				/* @var $productData \SS6\ShopBundle\Model\Product\ProductData */

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
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	private function disableIrrelevantFields(FormBuilderInterface $builder, Product $product) {
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
