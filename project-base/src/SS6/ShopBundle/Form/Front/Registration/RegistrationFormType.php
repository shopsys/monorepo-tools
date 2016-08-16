<?php

namespace SS6\ShopBundle\Form\Front\Registration;

use SS6\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
use SS6\ShopBundle\Component\Constraints\NotIdenticalToEmailLocalPart;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Form\TimedFormTypeExtension;
use SS6\ShopBundle\Model\Customer\UserData;
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
			->add('firstName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Jméno nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('lastName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím příjmení']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Příjmení nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('email', FormType::EMAIL, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Email(['message' => 'Vyplňte prosím platný e-mail']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'E-mail nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('password', FormType::REPEATED, [
				'type' => FormType::PASSWORD,
				'options' => [
					'attr' => ['autocomplete' => 'off'],
				],
				'first_options' => [
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Vyplňte prosím heslo']),
						new Constraints\Length(['min' => 6, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
				],
				'invalid_message' => 'Hesla se neshodují',
			])
			->add('email2', FormType::HONEY_POT)
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'registration_form';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => UserData::class,
			'attr' => ['novalidate' => 'novalidate'],
			TimedFormTypeExtension::OPTION_ENABLED => true,
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
