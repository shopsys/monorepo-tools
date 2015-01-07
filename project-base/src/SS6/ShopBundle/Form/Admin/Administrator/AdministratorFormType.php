<?php

namespace SS6\ShopBundle\Form\Admin\Administrator;

use SS6\ShopBundle\Model\Administrator\AdministratorData;
use SS6\ShopBundle\Component\Constraints\FieldsAreNotIdentical;
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
		return 'administrator';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('userName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím přihlašovací jméno')),
				)
			))
			->add('realName', 'text', array(
				'constraints' => array(
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím jméno')),
				)
			))
			->add('email', 'email', array(
				'required' => true,
				'constraints' => array(
					new Constraints\Email(array('message' => 'Vyplňte prosím platný e-mail')),
					new Constraints\NotBlank(array('message' => 'Vyplňte prosím e-mail')),
				)
			))
			->add('password', 'repeated', array(
				'type' => 'password',
				'required' => $this->scenario === self::SCENARIO_CREATE,
				'options' => array(
					'attr' => array('autocomplete' => 'off'),
				),
				'first_options' => array(
					'constraints' => array(
						new Constraints\NotBlank(array(
							'message' => 'Vyplňte prosím heslo',
							'groups' => array('create'),
						)),
						new Constraints\Length(array('min' => 6, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků')),
					)
				),
				'invalid_message' => 'Hesla se neshodují',
			))
			->add('save', 'submit');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => AdministratorData::class,
			'attr' => array('novalidate' => 'novalidate'),
			'constraints' => array(
				new FieldsAreNotIdentical(array(
					'field1' => 'userName',
					'field2' => 'password',
					'errorPath' => 'password',
					'message' => 'Heslo nesmí být stejné jako přihlašovací jméno',
				)),
			),
		));
	}

}
