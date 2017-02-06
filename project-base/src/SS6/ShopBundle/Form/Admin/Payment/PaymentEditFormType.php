<?php

namespace SS6\ShopBundle\Form\Admin\Payment;

use SS6\ShopBundle\Form\FormType;
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
		return 'payment_edit_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('paymentData', $this->paymentFormTypeFactory->create())
			->add($this->getPricesBuilder($builder))
			->add('save', FormType::SUBMIT);
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
				->add($currency->getId(), FormType::MONEY, [
					'currency' => false,
					'precision' => 6,
					'required' => true,
					'invalid_message' => 'Please enter price in correct format (positive number with decimal separator)',
					'constraints' => [
						new Constraints\NotBlank(['message' => 'Please enter price']),
						new Constraints\GreaterThanOrEqual([
							'value' => 0,
							'message' => 'Price must be greater or equal to {{ compared_value }}',
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
