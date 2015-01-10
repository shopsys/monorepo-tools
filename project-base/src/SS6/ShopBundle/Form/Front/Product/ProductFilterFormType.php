<?php

namespace SS6\ShopBundle\Form\Front\Product;

use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductFilterFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice[]
	 */
	private $parameterFilterChoices;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Filter\ParameterFilterChoice[] $parameterFilterChoices
	 */
	public function __construct(array $parameterFilterChoices) {
		$this->parameterFilterChoices = $parameterFilterChoices;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('minimalPrice', 'money', ['required' => false])
			->add('maximalPrice', 'money', ['required' => false])
			->add('parameters', new ParameterFilterFormType($this->parameterFilterChoices), [
				'required' => false,
			])
			->add('search', 'submit');
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
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
			'data_class' => ProductFilterData::class,
		));
	}

}
