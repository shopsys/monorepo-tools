<?php

namespace SS6\ShopBundle\Form\Admin\Pricing\Currency;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CurrencyFormType extends AbstractType {

	const EXCHANGE_RATE_IS_READ_ONLY = true;

	/**
	 * @var boolean
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
		return 'currency';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('name', FormType::TEXT, [
				'required' => true,
			])
			->add('code', FormType::CURRENCY, [
				'required' => true,
			])
			->add('symbol', FormType::TEXT, [
				'required' => true,
			])
			->add('exchangeRate', FormType::NUMBER, [
				'required' => true,
				'read_only' => $this->isRateReadOnly,
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
