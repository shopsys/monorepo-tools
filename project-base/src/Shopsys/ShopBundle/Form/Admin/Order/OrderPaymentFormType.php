<?php

namespace Shopsys\ShopBundle\Form\Admin\Order;

use Shopsys\ShopBundle\Form\FormType;
use Shopsys\ShopBundle\Model\Order\Item\OrderPaymentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class OrderPaymentFormType extends AbstractType {

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	/**
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $payments
	 */
	public function __construct(array $payments) {
		$this->payments = $payments;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'order_payment_form';
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('payment', FormType::CHOICE, [
				'required' => true,
				'choice_list' => new ObjectChoiceList($this->payments, 'name', [], null, 'id'),
				'error_bubbling' => true,
			])
			->add('priceWithVat', FormType::MONEY, [
				'currency' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter unit price with VAT']),
				],
				'error_bubbling' => true,
			])
			->add('vatPercent', FormType::MONEY, [
				'currency' => false,
				'constraints' => [
					new Constraints\NotBlank(['message' => 'Please enter VAT rate']),
				],
				'error_bubbling' => true,
			]);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'data_class' => OrderPaymentData::class,
			'attr' => ['novalidate' => 'novalidate'],
		]);
	}

}
