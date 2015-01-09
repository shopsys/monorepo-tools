<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class DeliveryAddressFormType extends AbstractType {

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
			->add('addressFilled', 'checkbox', array('required' => false))
			->add('companyName', 'text', array('required' => false))
			->add('contactPerson', 'text', array('required' => false))
			->add('telephone', 'text', array('required' => false))
			->add('street', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím ulici',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			))
			->add('city', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím město',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			))
			->add('postcode', 'text', array(
				'required' => true,
				'constraints' => array(
					new Constraints\NotBlank(array(
						'message' => 'Vyplňte prosím PSČ',
						'groups' => array('differentDeliveryAddress'),
					)),
				),
			));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => DeliveryAddressData::class,
			'attr' => array('novalidate' => 'novalidate'),
			'validation_groups' => function(FormInterface $form) {
				$validationGroups = array('Default');

				$deliveryAddressData = $form->getData();
				/* @var $deliveryAddressData \SS6\ShopBundle\Model\Customer\DeliveryAddressData */

				if ($deliveryAddressData->addressFilled) {
					$validationGroups[] = 'differentDeliveryAddress';
				}

				return $validationGroups;
			},
		));
	}

}
