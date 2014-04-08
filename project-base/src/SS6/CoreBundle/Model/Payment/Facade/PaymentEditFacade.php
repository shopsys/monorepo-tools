<?php

namespace SS6\CoreBundle\Model\Payment\Facade;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Payment\Entity\Payment;
use SS6\CoreBundle\Model\Payment\Repository\PaymentRepository;

class PaymentEditFacade {
	
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em, PaymentRepository $paymentRepository) {
		$this->em = $em;
		$this->paymentRepository = $paymentRepository;
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Payment\Entity\Payment $payment
	 */
	public function create(Payment $payment) {
		$this->em->persist($payment);
		$this->em->flush();
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Payment\Entity\Payment $payment
	 */
	public function edit(Payment $payment) {
		$this->em->persist($payment);
		$this->em->flush();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Payment\Entity\Payment
	 */
	public function getById($id) {
		return $this->paymentRepository->getById($id);
	}
	
	/**
	 * @param int $id
	 */
	public function deleteById($id) {
		$payment = $this->getById($id);
		$payment->markAsDeleted();
		$this->em->flush();
	}
}
