<?php

namespace SS6\ShopBundle\Form\Admin\Administrator;

use SS6\ShopBundle\Component\Constraints\Email;
use SS6\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Administrator\AdministratorData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class AdministratorFormType extends AbstractType {

	const SCENARIO_CREATE = 'create';
	const SCENARIO_EDIT = 'edit';

	private $scenario;

	public function __construct($scenario) {
		$this->scenario = $scenario;
	}

	public function getName() {
		return 'administrator_form';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('username', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím přihlašovací jméno']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Jméno nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('realName', FormType::TEXT, [
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím jméno']),
					new Constraints\Length(['max' => 100, 'maxMessage' => 'Jméno nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('email', FormType::EMAIL, [
				'required' => true,
				'constraints' => [
					new Email(['message' => 'Vyplňte prosím platný e-mail']),
					new Constraints\NotBlank(['message' => 'Vyplňte prosím e-mail']),
					new Constraints\Length(['max' => 255, 'maxMessage' => 'E-mail nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('password', FormType::REPEATED, [
				'type' => FormType::PASSWORD,
				'required' => $this->scenario === self::SCENARIO_CREATE,
				'options' => [
					'attr' => ['autocomplete' => 'off'],
				],
				'first_options' => [
					'constraints' => [
						new Constraints\NotBlank([
							'message' => 'Vyplňte prosím heslo',
							'groups' => [self::SCENARIO_CREATE],
						]),
						new Constraints\Length(['min' => 6, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
				],
				'invalid_message' => 'Hesla se neshodují',
			])
			->add('save', FormType::SUBMIT);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => AdministratorData::class,
			'attr' => ['novalidate' => 'novalidate'],
			'constraints' => [
				new FieldsAreNotIdentical([
					'field1' => 'username',
					'field2' => 'password',
					'errorPath' => 'password',
					'message' => 'Heslo nesmí být stejné jako přihlašovací jméno',
				]),
			],
		]);
	}

}
