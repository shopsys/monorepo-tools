<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Customer\UserData;
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
			->add('firstName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
				],
			])
			->add('lastName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím příjmení']),
				],
			])
			->add('email', FormType::EMAIL, ['read_only' => true, 'required' => false])
			->add('password', FormType::REPEATED, [
				'type' => FormType::PASSWORD,
				'required' => false,
				'first_options' => [
					'constraints' => [
						new Constraints\Length(['min' => 5, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
					'attr' => ['autocomplete' => 'off'],
				],
				'invalid_message' => 'Hesla se neshodují',
			]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => UserData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
