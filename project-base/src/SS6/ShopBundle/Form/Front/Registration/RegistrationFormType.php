<?php

namespace SS6\ShopBundle\Form\Front\Registration;

use SS6\ShopBundle\Model\Customer\CustomerFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class RegistrationFormType extends AbstractType {
	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím jméno')),
				)
			))
			->add('lastName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím příjmení')),
				)
			))
			->add('email', 'email', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím e-mail')),
					new Constraints\Email(array('message' => 'Vyplňte prosím platný e-mail')),
				)
			))
			->add('password', 'repeated', array(
				'type' => 'password',
				'first_options' => array(
					'constraints' => array(
						new Constraints\NotBlank(array('message' => 'Vyplňte prosím heslo')),
						new Constraints\Length(array('min' => 5, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků')),
					)
				),
				'invalid_message' => 'Hesla se neshodují',
			))
			->add('save', 'submit');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'registration';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => CustomerFormData::class,
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
