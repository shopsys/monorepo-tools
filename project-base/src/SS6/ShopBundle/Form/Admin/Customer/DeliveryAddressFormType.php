<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class DeliveryAddressFormType extends AbstractType {

	const VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS = 'differentDeliveryAddress';

	/**
	 * @return string
	 */
	public function getName() {
		return 'deliveryAddress';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('addressFilled', 'checkbox', ['required' => false])
			->add('companyName', 'text', ['required' => false])
			->add('contactPerson', 'text', ['required' => false])
			->add('telephone', 'text', ['required' => false])
			->add('street', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím ulici',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('city', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím město',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			])
			->add('postcode', 'text', [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank([
						'message' => 'Vyplňte prosím PSČ',
						'groups' => [self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS],
					]),
				],
			]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => DeliveryAddressData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'validation_groups' => function (FormInterface $form) {
				$validationGroups = ['Default'];

				$deliveryAddressData = $form->getData();
				/* @var $deliveryAddressData \SS6\ShopBundle\Model\Customer\DeliveryAddressData */

				if ($deliveryAddressData->addressFilled) {
					$validationGroups[] = self::VALIDATION_GROUP_DIFFERENT_DELIVERY_ADDRESS;
				}

				return $validationGroups;
			},
		]);
	}

}
