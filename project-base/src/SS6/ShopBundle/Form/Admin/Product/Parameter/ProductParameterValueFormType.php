<?php

namespace SS6\ShopBundle\Form\Admin\Product\Parameter;

use SS6\ShopBundle\Model\Product\Parameter\ProductParameterValuesLocalizedData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ProductParameterValueFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\Parameter[]
	 */
	private $parameters;

	public function __construct(array $parameters) {
		$this->parameters = $parameters;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'productParameterValue';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('parameter', 'choice', array(
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->parameters, 'name', array(), null, 'id'),
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyberte parametr')),
				),
			))
			->add('valueText', 'localized', array(
				'required' => true,
				'main_constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte hodnotu parametru')),
				),
			));
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
			'data_class' => ProductParameterValuesLocalizedData::class,
		));
	}

}
