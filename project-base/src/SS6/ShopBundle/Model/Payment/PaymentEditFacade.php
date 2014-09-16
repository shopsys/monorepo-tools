<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class PaymentEditFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentRepository
	 */
	private $paymentRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportRepository
	 */
	private $transportRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 */
	public function __construct(EntityManager $em, PaymentRepository $paymentRepository, TransportRepository $transportRepository) {
		$this->em = $em;
		$this->paymentRepository = $paymentRepository;
		$this->transportRepository = $transportRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function create(PaymentData $paymentData) {
		$payment = new Payment($paymentData);
		$this->em->persist($payment);
		$this->setAddionalDataAndFlush($payment, $paymentData);
		
		return $payment;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	public function edit(Payment $payment, PaymentData $paymentData) {
		$payment->edit($paymentData);
		$this->setAddionalDataAndFlush($payment, $paymentData);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getById($id) {
		return $this->paymentRepository->getById($id);
	}

	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getByIdWithTransports($id) {
		return $this->paymentRepository->getByIdWithTransports($id);
	}

	/**
	 * @param int $id
	 */
	public function deleteById($id) {
		$payment = $this->getById($id);
		$payment->markAsDeleted();
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Payment\PaymentData $paymentData
	 */
	private function setAddionalDataAndFlush(Payment $payment, PaymentData $paymentData) {
		$transports = $this->transportRepository->findAllByIds($paymentData->getTransports());
		$payment->setTransports($transports);
		$payment->setImageForUpload($paymentData->getImage());
		$this->em->flush();
	}
}
