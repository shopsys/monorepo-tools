<?php

namespace SS6\ShopBundle\Form\Front\Login;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
						new Constraints\NotBlank(),
						new Constraints\Email(),
					)
				)
			)
			->add('password', 'password', array(
					'constraints' => array(
						new Constraints\NotBlank(),
					)
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
	 * @param array $options
	 * @return array
	 */
	public function getDefaultOptions(array $options) {
		return array();
	}

}
