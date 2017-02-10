<?php

namespace Shopsys\ShopBundle\Form\Admin\Product\Availability;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class AvailabilityFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'availability_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::LOCALIZED, [
				'required' => true,
				'options' => [
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Please enter availability name in all languages']),
						new Constraints\Length(['max' => 100, 'maxMessage' => 'Availability name cannot be longer than {{ limit }} characters']),
					],
				],
			])
			->add('dispatchTime', FormType::NUMBER, [
				'precision' => 0,
				'required' => false,
				'invalid_message' => 'Number of days to expedite must be whole number.',
				'constraints' => [
					new Constraints\GreaterThanOrEqual([
						'value' => 0, 'message' => 'Number of days to despatch must be greater or equal to {{ compared_value }}',
					]),
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
