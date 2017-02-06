<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use SS6\ShopBundle\Component\Constraints\Email;
use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class MailSettingFormType extends AbstractType {

	/**
	 * @return string
	 */
	public function getName() {
		return 'mail_setting_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('email', FormType::EMAIL, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter e-mail']),
					new Email(['message' => 'Please enter valid e-mail']),
				],
			])
			->add('name', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter full name']),
				],
			])
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
