<?php

namespace SS6\ShopBundle\Form\Admin\Product\Availability;

use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class AvailabilityFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'availability';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', 'localized', array(
				'required' => true,
				'options' => array(
					'constraints' => array(
						new Constraints\NotBlank(array('message' => 'Vyplňte prosím název dostupnosti ve všech jazycích')),
						new Constraints\Length(array('max' => 100, 'maxMessage' => 'Název dostupnosti nesmí být delší než {{ limit }} znaků')),
					),
				),
			));
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => AvailabilityData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
