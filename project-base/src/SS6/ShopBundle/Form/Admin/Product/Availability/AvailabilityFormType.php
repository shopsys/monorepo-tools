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
			->add('name', 'localized', [
				'required' => true,
				'options' => [
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Vyplňte prosím název dostupnosti ve všech jazycích']),
						new Constraints\Length(['max' => 100, 'maxMessage' => 'Název dostupnosti nesmí být delší než {{ limit }} znaků']),
					],
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => AvailabilityData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
