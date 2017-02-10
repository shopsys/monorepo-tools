<?php

namespace Shopsys\ShopBundle\Form\Admin\Login;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Administrator\Administrator;
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
			->add('username', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter login']),
				],
			])
			->add('password', FormType::PASSWORD, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter password']),
				],
			])
			->add('login', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'admin_login_form';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => Administrator::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
