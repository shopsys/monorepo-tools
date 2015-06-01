<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductFilterFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice[]
	 */
	private $parameterFilterChoices;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	private $flagFilterChoices;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice[] $parameterFilterChoices
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flagFilterChoices
	 */
	public function __construct(array $parameterFilterChoices, array $flagFilterChoices) {
		$this->parameterFilterChoices = $parameterFilterChoices;
		$this->flagFilterChoices = $flagFilterChoices;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('minimalPrice', FormType::MONEY, [
				'currency' => false,
				'precision' => 2,
				'required' => false,
				'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
				'constraints' => [
					new Constraints\GreaterThanOrEqual([
						'value' => 0,
						'message' => 'Cena musí být větší nebo rovna {{ compared_value }}',
					]),
				],
			])
			->add('maximalPrice', FormType::MONEY, [
				'currency' => false,
				'precision' => 2,
				'required' => false,
				'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
				'constraints' => [
					new Constraints\GreaterThanOrEqual([
						'value' => 0,
						'message' => 'Cena musí být větší nebo rovna {{ compared_value }}',
					]),
				],
			])
			->add('parameters', new ParameterFilterFormType($this->parameterFilterChoices), [
				'required' => false,
			])
			->add('inStock', FormType::CHECKBOX, ['required' => false])
			->add('flags', FormType::CHOICE, [
				'required' => false,
				'expanded' => true,
				'multiple' => true,
				'choice_list' => new ObjectChoiceList($this->flagFilterChoices, 'name')
			])
			->add('search', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'productFilter';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'data_class' => ProductFilterData::class,
			'method' => 'GET',
			'csrf_protection' => false,
		]);
	}

}
