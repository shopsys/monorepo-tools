<?php

namespace SS6\ShopBundle\Form\Admin\Customer;

use SS6\ShopBundle\Component\Constraints\Email;
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
	 * @var string
	 */
	private $scenario;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\SelectedDomain
	 */
	private $selectedDomain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]
	 */
	private $pricingGroups;

	/**
	 * @param string $scenario
	 * @param \SS6\ShopBundle\Component\Domain\SelectedDomain $selectedDomain
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup[]|null $pricingGroups
	 */
	public function __construct($scenario, $selectedDomain = null, $pricingGroups = null) {
		$this->scenario = $scenario;
		$this->selectedDomain = $selectedDomain;
		$this->pricingGroups = $pricingGroups;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'user_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
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
					new Constraints\Length(['max' => 255, 'maxMessage' => 'E-mail nesmí být delší než {{ limit }} znaků']),
					new Email(['message' => 'Vyplňte prosím platný e-mail']),
				],
			])
			->add('password', FormType::REPEATED, [
				'type' => FormType::PASSWORD,
				'required' => $this->scenario === CustomerFormType::SCENARIO_CREATE,
				'options' => [
					'attr' => ['autocomplete' => 'off'],
				],
				'first_options' => [
					'constraints' => [
						new Constraints\NotBlank([
							'message' => 'Vyplňte prosím heslo',
							'groups' => [CustomerFormType::SCENARIO_CREATE],
						]),
						new Constraints\Length(['min' => 6, 'minMessage' => 'Heslo musí mít minimálně {{ limit }} znaků']),
					],
				],
				'invalid_message' => 'Hesla se neshodují',
			]);

		if ($this->scenario === CustomerFormType::SCENARIO_CREATE) {
			$builder
				->add('domainId', FormType::DOMAIN, [
					'required' => true,
					'data' => $this->selectedDomain->getId(),
				]);
		}

		$builder
			->add('pricingGroup', FormType::CHOICE, [
				'required' => true,
				'choices' => $this->pricingGroups,
				'choices_as_values' => true,
				'choice_label' => 'name',
				'choice_value' => 'id',
				'group_by' => 'domainId',
			]);
	}

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
