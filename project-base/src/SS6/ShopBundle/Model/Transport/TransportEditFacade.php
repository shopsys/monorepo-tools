<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Image\ImageFacade;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;
use SS6\ShopBundle\Model\Transport\TransportRepository;
use SS6\ShopBundle\Model\Transport\TransportVisibilityCalculation;

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
	 * @var \SS6\ShopBundle\Model\Transport\TransportVisibilityCalculation
	 */
	private $transportVisibilityCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFacade
	 */
	private $imageFacade;

	public function __construct(
		EntityManager $em,
		TransportRepository $transportRepository,
		PaymentRepository $paymentRepository,
		TransportVisibilityCalculation $transportVisibilityCalculation,
		Domain $domain,
		ImageFacade $imageFacade
	) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
		$this->paymentRepository = $paymentRepository;
		$this->transportVisibilityCalculation = $transportVisibilityCalculation;
		$this->domain = $domain;
		$this->imageFacade = $imageFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function create(TransportData $transportData) {
		$transport = new Transport($transportData);
		$this->em->persist($transport);
		$this->em->beginTransaction();
		$this->em->flush();
		$this->createTransportDomains($transport, $transportData->getDomains());
		$this->imageFacade->uploadImage($transport, $transportData->getImage(), null);
		$this->em->flush();
		$this->em->commit();

		return $transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Transport\TransportData $transportData
	 */
	public function edit(Transport $transport, TransportData $transportData) {
		$transport->edit($transportData);

		$this->em->beginTransaction();
		$this->deleteTransportDomainsByTransport($transport);
		$this->createTransportDomains($transport, $transportData->getDomains());
		$this->imageFacade->uploadImage($transport, $transportData->getImage(), null);
		$this->em->flush();
		$this->em->commit();
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
		$this->em->beginTransaction();
		$this->deleteTransportDomainsByTransport($transport);
		$this->em->flush();
		$this->em->commit();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param array $domainIds
	 */
	private function createTransportDomains(Transport $transport, array $domainIds) {
		foreach ($domainIds as $domainId) {
			$transportDomain = new TransportDomain($transport, $domainId);
			$this->em->persist($transportDomain);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	private function deleteTransportDomainsByTransport(Transport $transport) {
		$transportDomains = $this->getTransportDomainsByTransport($transport);
		foreach ($transportDomains as $transportDomain) {
			$this->em->remove($transportDomain);
		}
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $visiblePayments
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getVisibleOnCurrentDomain(array $visiblePayments) {
		$transports = $this->transportRepository->getAllByDomainId($this->domain->getId());

		return $this->transportVisibilityCalculation->filterVisible($transports, $visiblePayments, $this->domain->getId());
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

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\TransportDomain[]
	 */
	public function getTransportDomainsByTransport(Transport $transport) {
		return $this->transportRepository->getTransportDomainsByTransport($transport);
	}
}
