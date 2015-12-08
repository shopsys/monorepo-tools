<?php

namespace SS6\ShopBundle\Form\Front\Customer;

use SS6\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
use SS6\ShopBundle\Component\Constraints\NotIdenticalToEmailLocalPart;
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
		return 'user_form';
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
				'options' => [
					'attr' => ['autocomplete' => 'off'],
				],
				'first_options' => [
					'constraints' => [
						new Constraints\Length(['min' => 6, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
					'attr' => ['autocomplete' => 'off'],
				],
				'invalid_message' => 'Hesla se neshodují',
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => UserData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'constraints' => [
				new FieldsAreNotIdentical([
					'field1' => 'email',
					'field2' => 'password',
					'errorPath' => 'password',
					'message' => 'Heslo nesmí být stejné jako přihlašovací e-mail.',
				]),
				new NotIdenticalToEmailLocalPart([
					'password' => 'password',
					'email' => 'email',
					'errorPath' => 'password',
					'message' => 'Heslo nesmí být stejné jako část e-mailu před zavináčem.',
				]),
			],
		]);
	}

}
