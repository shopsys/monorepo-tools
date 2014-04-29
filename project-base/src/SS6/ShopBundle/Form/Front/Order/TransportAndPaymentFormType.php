<?php

namespace SS6\ShopBundle\Form\Front\Order;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class TransportAndPaymentFormType extends AbstractType {
	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	private $transports;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment[]
	 */
	private $payments;

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
			->add('transport', new TransportFormType($this->transports), array(
				'constraints' => array(
					new Constraints\NotNull(array('message' => 'Vyberte prosím dopravu')),
				)
			))
			->add('payment', new PaymentFromType($this->payments), array(
				'constraints' => array(
					new Constraints\NotNull(array('message' => 'Vyberte prosím platbu')),
				)
			))
			->add('submit', 'submit');
	}

	public function getName() {
		return 'orderTransportAndPayment';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'attr' => array('novalidate' => 'novalidate'),
		));
	}

}
