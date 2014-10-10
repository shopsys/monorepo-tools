<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportRepository;
use SS6\ShopBundle\Model\Transport\VisibilityCalculation;

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
	 * @var \SS6\ShopBundle\Model\Transport\VisibilityCalculation
	 */
	private $visibilityCalculation;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Transport\TransportRepository $transportRepository
	 * @param \SS6\ShopBundle\Model\Payment\PaymentRepository $paymentRepository
	 */
	public function __construct(
		EntityManager $em,
		TransportRepository $transportRepository,
		PaymentRepository $paymentRepository,
		VisibilityCalculation $visibilityCalculation
	) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
		$this->paymentRepository = $paymentRepository;
		$this->visibilityCalculation = $visibilityCalculation;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function create(TransportData $transportData) {
		$transport = new Transport($transportData);
		$this->em->persist($transport);
		$this->setAdditionalDataAndFlush($transport, $transportData);
		
		return $transport;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	public function edit(Transport $transport, TransportData $transportData) {
		$transport->edit($transportData);
		$this->setAdditionalDataAndFlush($transport, $transportData);
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
		$paymentsByTransport = $this->paymentRepository->findAllByTransport($transport);
		foreach ($paymentsByTransport as $payment) {
			/* @var $payment \SS6\ShopBundle\Model\Payment\Payment */
			$payment->getTransports()->removeElement($transport);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	private function setAdditionalDataAndFlush(Transport $transport, TransportData $transportData) {
		$transport->setImageForUpload($transportData->getImage());
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $visiblePayments
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getVisible(array $visiblePayments) {
		$transports = $this->transportRepository->findAll();

		return $this->visibilityCalculation->findAllVisible($transports, $visiblePayments);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $oldVat
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $newVat
	 */
	public function replaceOldVatWithNewVat(Vat $oldVat, Vat $newVat) {
		$transports = $this->transportRepository->getAllIncludingDeletedByVat($oldVat);
		foreach ($transports as $transport) {
			$transport->changeVat($newVat);
		}
		$this->em->flush();
	}
}
