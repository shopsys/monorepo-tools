<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Model\Payment\PaymentEditData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class PaymentEditFormType extends AbstractType {

	/**
	 * @var \SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory
	 */
	private $paymentFormTypeFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	private $currencies;

	/**
	 * @param \SS6\ShopBundle\Form\Admin\Payment\PaymentFormTypeFactory $paymentFormTypeFactory
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency[] $currencies
	 */
	public function __construct(PaymentFormTypeFactory $paymentFormTypeFactory, array $currencies) {
		$this->paymentFormTypeFactory = $paymentFormTypeFactory;
		$this->currencies = $currencies;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'payment_edit';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('paymentData', $this->paymentFormTypeFactory->create())
			->add($this->getPricesBuilder($builder))
			->add('save', 'submit');
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @return \Symfony\Component\Form\FormBuilderInterface
	 */
	private function getPricesBuilder(FormBuilderInterface $builder) {
		$pricesBuilder = $builder->create('prices', null, [
			'compound' => true,
		]);
		foreach ($this->currencies as $currency) {
			$pricesBuilder
				->add($currency->getId(), 'money', [
					'currency' => false,
					'precision' => 6,
					'required' => true,
					'invalid_message' => 'Prosím zadejte cenu v platném formátu (kladné číslo s desetinnou čárkou nebo tečkou)',
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Prosím vyplňte cenu']),
						new Constraints\GreaterThanOrEqual([
							'value' => 0,
							'message' => 'Cena musí být větší nebo rovna {{ compared_value }}',
						]),

					],
				]);
		}

		return $pricesBuilder;
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => PaymentEditData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}
}
