<?php

namespace SS6\ShopBundle\Form\Admin\Pricing\Group;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PricingGroupFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'pricing_group';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'text', array(
				'required' => false,
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím název cenové skupiny')),
				),
			))
			->add('coefficient', 'number', array(
				'required' => true,
				'precision' => 4,
				'invalid_message' => 'Prosím zadejte koeficient v platném formátu',
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím koeficient cenové skupiny')),
					new Constraints\GreaterThan(array('value' => 0, 'message' => 'Koeficient musí být větší než 0')),
				),
			));
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => PricingGroupData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}
}
