<?php

namespace SS6\ShopBundle\Form\Front\Login;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class LoginFormType extends AbstractType {

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email', 'text', array(
					'constraints' => array(
						new Constraints\NotBlank(array('message' => 'Vyplňte prosím email')),
						new Constraints\Email(),
					),
				)
			)
			->add('password', 'password', array(
					'constraints' => array(
						new Constraints\NotBlank(array('message' => 'Vyplňte prosím heslo')),
					),
				)
			)
			->add('login', 'submit');
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'front_login';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
