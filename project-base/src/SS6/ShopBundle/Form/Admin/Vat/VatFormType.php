<?php

namespace SS6\ShopBundle\Form\Admin\Vat;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class VatFormType extends AbstractType {

	const SCENARIO_CREATE = 1;
	const SCENARIO_EDIT = 2;

	/**
	 * @var bool
	 */
	private $scenario;

	/**
	 * @param int $scenario
	 */
	public function __construct($scenario) {
		$this->scenario = $scenario;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'vat_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím název dph']),
					new Constraints\Length(['max' => 50, 'maxMessage' => 'Název DPH nesmí být delší než {{ limit }} znaků']),
				],
			])
			->add('percent', FormType::NUMBER, [
				'required' => false,
				'precision' => 4,
				'disabled' => $this->scenario === self::SCENARIO_EDIT,
				'read_only' => $this->scenario === self::SCENARIO_EDIT,
				'invalid_message' => 'Prosím zadejte DPH v platném formátu',
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Vyplňte prosím výši DPH']),
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => VatData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
