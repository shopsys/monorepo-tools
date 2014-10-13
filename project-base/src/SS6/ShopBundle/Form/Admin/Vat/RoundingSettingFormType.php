<?php

namespace SS6\ShopBundle\Form\Admin\Vat;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoundingSettingFormType extends AbstractType {

	/**
	 * @var array
	 */
	private $roundingTypes;

	/**
	 * @param array $roundingTypes
	 */
	public function __construct(array $roundingTypes) {
		$this->roundingTypes = $roundingTypes;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'rounding_setting';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('roundingType', 'choice', array(
				'required' => true,
				'choices' => $this->roundingTypes,
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
