<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Model\Customer\UserFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class UserFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'user';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím jméno')),
				),
			))
			->add('lastName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím příjmení')),
				),
			))
			->add('email', 'email', array('read_only' => true, 'required' => false))
			->add('password', 'repeated', array(
				'type' => 'password',
				'required' => false,
				'first_options' => array(
					'constraints' => array(
						new Constraints\Length(array('min' => 5, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků')),
					),
					'attr' => array('autocomplete' => 'off'),
				),
				'invalid_message' => 'Hesla se neshodují',
			));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => UserFormData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
