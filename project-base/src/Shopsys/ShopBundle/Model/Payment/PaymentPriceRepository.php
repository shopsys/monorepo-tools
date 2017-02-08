<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Payment\PaymentPrice;

class PaymentPriceRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getPaymentPriceRepository() {
		return $this->em->getRepository(PaymentPrice::class);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @return \SS6\ShopBundle\Model\Payment\PaymentPrice[]
	 */
	public function getAllByPayment(Payment $payment) {
		return $this->getPaymentPriceRepository()->findBy(['payment' => $payment]);
	}

}
