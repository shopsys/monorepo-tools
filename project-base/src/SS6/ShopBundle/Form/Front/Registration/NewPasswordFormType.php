<?php

namespace SS6\ShopBundle\Form\Front\Registration;

use SS6\ShopBundle\Form\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class NewPasswordFormType extends AbstractType {

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('newPassword', FormType::REPEATED, [
				'type' => FormType::PASSWORD,
				'first_options' => [
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Vyplňte prosím heslo']),
						new Constraints\Length(['min' => 5, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
				],
				'invalid_message' => 'Hesla se neshodují',
			])
			->add('submit', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'reset_password';
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
