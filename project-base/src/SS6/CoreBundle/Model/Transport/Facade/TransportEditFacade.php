<?php

namespace SS6\CoreBundle\Model\Transport\Facade;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Payment\Repository\PaymentRepository;
use SS6\CoreBundle\Model\Transport\Entity\Transport;
use SS6\CoreBundle\Model\Transport\Repository\TransportRepository;

class TransportEditFacade {
	
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @var PaymentRepository
	 */
	private $paymentRepository;
	
	/**
	 * @var TransportRepository
	 */
	private $transportRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em, TransportRepository $transportRepository, PaymentRepository $paymentRepository) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
		$this->paymentRepository = $paymentRepository;
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 */
	public function create(Transport $transport) {
		$this->em->persist($transport);
		$this->em->flush();
	}
	
	/**
	 * @param \SS6\CoreBundle\Model\Transport\Entity\Transport $transport
	 */
	public function edit(Transport $transport) {
		$this->em->persist($transport);
		$this->em->flush();
	}
	
	/**
	 * @param int $id
	 * @return \SS6\CoreBundle\Model\Transport\Entity\Transport
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
			/* @var $payment \SS6\CoreBundle\Model\Payment\Entity\Payment */
			$payment->getTransports()->removeElement($transport);
		}
		$this->em->flush();
	}
}
