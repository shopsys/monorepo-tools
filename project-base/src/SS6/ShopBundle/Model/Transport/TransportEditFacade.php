<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Form\Admin\Transport\TransportFormData;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportRepository;

class TransportEditFacade {
	
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
	 * @param \SS6\ShopBundle\Model\Transport\TransportRepository $transportRepository
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 */
	public function __construct(EntityManager $em, TransportRepository $transportRepository, PaymentRepository $paymentRepository) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
		$this->paymentRepository = $paymentRepository;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function create(Transport $transport) {
		$this->em->persist($transport);
		$this->em->flush();
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function edit(Transport $transport) {
		$this->em->flush();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getById($id) {
		return $this->transportRepository->getById($id);
	}
	
	/**
	 * @param int $id
	 */
	public function deleteById($id) {
		$transport = $this->getById($id);
		$transport->markAsDeleted();
		$paymentsByTransport = $this->paymentRepository->getAllByTransport($transport);
		foreach ($paymentsByTransport as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			$payment->getTransports()->removeElement($transport);
		}
		$this->em->flush();
	}
}
