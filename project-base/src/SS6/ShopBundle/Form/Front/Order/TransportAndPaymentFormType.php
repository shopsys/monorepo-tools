<?php

namespace SS6\ShopBundle\Form\Front\Order;

use SS6\ShopBundle\Form\FormType;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\ExecutionContextInterface;

class TransportAndPaymentFormType extends AbstractType {
	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport[]$transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	public function __construct(array $transports, array $payments) {
		$this->transports = $transports;
		$this->payments = $payments;
	}

	/**
	 * @param \Symfony\Component\Form\FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('transport', FormType::SINGLE_CHECKBOX_CHOICE, [
				'choice_list' => new ObjectChoiceList($this->transports, 'name', [], null, 'id'),
				'data_class' => Transport::class,
				'constraints' => [
					new Constraints\NotNull(['message' => 'Vyberte prosím dopravu']),
				],
				'invalid_message' => 'Vyberte prosím dopravu',
			])
			->add('payment', FormType::SINGLE_CHECKBOX_CHOICE, [
				'choice_list' => new ObjectChoiceList($this->payments, 'name', [], null, 'id'),
				'data_class' => Payment::class,
				'constraints' => [
					new Constraints\NotNull(['message' => 'Vyberte prosím platbu']),
				],
				'invalid_message' => 'Vyberte prosím platbu',
			])
			->add('save', FormType::SUBMIT);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'transport_and_payment_form';
	}

	/**
	 * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'attr' => ['novalidate' => 'novalidate'],
			'constraints' => [
				new Constraints\Callback([$this, 'validateTransportPaymentRelation']),
			],
		]);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \Symfony\Component\Validator\ExecutionContextInterface $context
	 */
	public function validateTransportPaymentRelation(OrderData $orderData, ExecutionContextInterface $context) {
		$payment = $orderData->payment;
		$transport = $orderData->transport;

		$relationExists = false;
		if ($payment instanceof Payment && $transport instanceof Transport) {
			if ($payment->getTransports()->contains($transport)) {
				$relationExists = true;
			}
		}

		if (!$relationExists) {
			$context->addViolation('Vyberte prosím platnou kombinaci dopravy a platby');
		}
	}

}
