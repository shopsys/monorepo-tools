<?php

namespace Shopsys\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Payment\PaymentPrice;

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
	 * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
	 * @return \Shopsys\ShopBundle\Model\Payment\PaymentPrice[]
	 */
	public function getAllByPayment(Payment $payment) {
		return $this->getPaymentPriceRepository()->findBy(['payment' => $payment]);
	}

}
