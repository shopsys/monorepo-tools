<?php

namespace SS6\ShopBundle\Form\Admin\Superadmin;

use SS6\ShopBundle\Model\Pricing\PricingSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class InputPriceTypeFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'inputPriceType';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('type', 'choice', array(
				'choices' => PricingSetting::getInputPriceTypes(),
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Prosím vyplňte typ vstupní ceny')),
				),
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
