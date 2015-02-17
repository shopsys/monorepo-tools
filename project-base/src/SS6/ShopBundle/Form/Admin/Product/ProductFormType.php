<?php

namespace SS6\ShopBundle\Form\Admin\Product;

use SS6\ShopBundle\Component\Constraints\NotSelectedDomainToShow;
use SS6\ShopBundle\Component\Transformers\InverseArrayValuesTransformer;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\ValidationGroup;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints;

class ProductFormType extends AbstractType {

	const VALIDATION_GROUP_AUTO_PRICE_CALCULATION = 'autoPriceCalculation';
	const VALIDATION_GROUP_USING_STOCK = 'usingStock';
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
	 * @var \SS6\ShopBundle\Model\Product\ProductDomainHiddenToShowTransformer
	 */
	private $inverseArrayValuesTransformer;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category[]
	 */
	private $categories;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	private $flags;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $translator;

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat[] $vats
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability[] $availabilities
	 * @param \SS6\ShopBundle\Model\Product\ProductDomainHiddenToShowTransformer $inverseArrayValuesTransformer
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flags
	 * @param \Symfony\Component\Translation\TranslatorInterface $translator
	 */
	public function __construct(
		array $vats,
		array $availabilities,
		InverseArrayValuesTransformer $inverseArrayValuesTransformer,
		array $categories,
		array $flags,
		TranslatorInterface $translator
	) {
		$this->vats = $vats;
		$this->availabilities = $availabilities;
		$this->inverseArrayValuesTransformer = $inverseArrayValuesTransformer;
		$this->categories = $categories;
		$this->flags = $flags;
		$this->translator = $translator;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'product';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::LOCALIZED, [
				'main_constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte název']),
				],
				'options' => ['required' => false],
			])
			->add(
				$builder
					->create('showOnDomains', FormType::DOMAINS, [
						'constraints' => [
							new NotSelectedDomainToShow(['message' => 'Musíte vybrat alespoň jednu doménu']),
						],
						'property_path' => 'hiddenOnDomains',
					])
					->addViewTransformer($this->inverseArrayValuesTransformer)
			)
			->add('hidden', FormType::YES_NO, ['required' => false])
			->add('catnum', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Katalogové číslo nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('partno', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Výrobní číslo nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('ean', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\Length(['max' => 100, 'maxMessage' => 'EAN nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('description', FormType::LOCALIZED, [
				'type' => FormType::WYSIWYG,
				'required' => false,
			])
			->add('usingStock', FormType::YES_NO, ['required' => false])
			->add('stockQuantity', FormType::INTEGER, [
				'required' => false,
				'invalid_message' => 'Prosím zadejte číslo',
			])
			->add('availability', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->availabilities, 'name', [], null, 'id'),
				'empty_value' => '-- Vyberte dostupnost --',
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Prosím vyberte dostupnost',
						'groups' => self::VALIDATION_GROUP_NOT_USING_STOCK,
					]),
				],
			])
			->add('outOfStockAvailability', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->availabilities, 'name', [], null, 'id'),
				'empty_value' => '-- Vyberte dostupnost --',
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Prosím vyberte dostupnost',
						'groups' => self::VALIDATION_GROUP_USING_STOCK,
					]),
				],
			])
			->add('price', FormType::MONEY, [
				'currency' => false,
				'precision' => 6,
				'required' => true,
				'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Prosím vyplňte cenu',
						'groups' => self::VALIDATION_GROUP_AUTO_PRICE_CALCULATION,
					]),
					new Constraints\GreaterThanOrEqual([
						'value' => 0,
						'message' => 'Cena musí být větší nebo rovna {{ compared_value }}',
						'groups' => self::VALIDATION_GROUP_AUTO_PRICE_CALCULATION,
					]),
				],
			])
			->add('vat', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->vats, 'name', [], null, 'id'),
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Prosím vyplňte výši DPH']),
				],
			])
			->add('sellingFrom', FormType::DATE_PICKER, [
				'required' => false,
				'constraints' => [
					new Constraints\Date(['message' => 'Datum zadávejte ve formátu dd.mm.rrrr']),
				],
				'invalid_message' => 'Datum zadávejte ve formátu dd.mm.rrrr',
			])
			->add('sellingTo', FormType::DATE_PICKER, [
				'required' => false,
				'constraints' => [
					new Constraints\Date(['message' => 'Datum zadávejte ve formátu dd.mm.rrrr']),
				],
				'invalid_message' => 'Datum zadávejte ve formátu dd.mm.rrrr',
			])
			->add('categories', FormType::CHOICE, [
				'required' => false,
				'choice_list' => new ObjectChoiceList($this->categories, 'name', [], null, 'id'),
				'multiple' => true,
				'expanded' => true,
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
					Product::PRICE_CALCULATION_TYPE_AUTO => $this->translator->trans('Automaticky'),
					Product::PRICE_CALCULATION_TYPE_MANUAL => $this->translator->trans('Ručně'),
				],
			]);
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

}
