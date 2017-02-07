<?php

namespace SS6\ShopBundle\Form\Admin\Pricing\Currency;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class CurrencyFormType extends AbstractType {

	const EXCHANGE_RATE_IS_READ_ONLY = true;
	const EXCHANGE_RATE_IS_NOT_READ_ONLY = false;

	/**
	 * @var bool
	 */
	private $isRateReadOnly;

	/**
	 * @param bool $isRateReadOnly
	 */
	public function __construct($isRateReadOnly) {
		$this->isRateReadOnly = $isRateReadOnly;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'currency_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter name']),
					new Constraints\Length(['max' => 50, 'maxMessage' => 'Name cannot be longer than {{ limit }} characters']),
				],
			])
			->add('code', FormType::CURRENCY, [
				'required' => true,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter currency code']),
					new Constraints\Length(['max' => 3, 'maxMessage' => 'Currency code cannot be longer than {{ limit }} characters']),
				],
			])
			->add('exchangeRate', FormType::NUMBER, [
				'required' => true,
				'precision' => 6,
				'read_only' => $this->isRateReadOnly,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter currency exchange rate']),
					new Constraints\GreaterThan(0),
				],
			]);
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => CurrencyData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}
}
