<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Form\Admin\Payment\PaymentFormData;
use SS6\ShopBundle\Model\Payment\Payment;
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
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 */
	public function create(Payment $payment) {
		$this->em->persist($payment);
		$this->em->flush();
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Form\Admin\Payment\PaymentFormData $paymentData
	 */
	public function edit(Payment $payment, PaymentFormData $paymentData) {
		$payment->setEdit(
			$paymentData->getName(),
			$paymentData->getPrice(),
			$paymentData->getDescription(),
			$paymentData->isHidden()
		);
		$transports = $this->transportRepository->findAllByIds($paymentData->getTransports());
		$payment->setTransports($transports);
		$payment->setImageForUpload($paymentData->getImage());
		$this->em->flush();
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
}
