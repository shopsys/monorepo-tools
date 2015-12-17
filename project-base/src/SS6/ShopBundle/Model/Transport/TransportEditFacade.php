<?php

namespace SS6\ShopBundle\Model\Transport;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Image\ImageFacade;
use SS6\ShopBundle\Model\Payment\PaymentRepository;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;
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
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Image\ImageFacade
	 */
	private $imageFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	public function __construct(
		EntityManager $em,
		TransportRepository $transportRepository,
		PaymentRepository $paymentRepository,
		TransportVisibilityCalculation $transportVisibilityCalculation,
		Domain $domain,
		ImageFacade $imageFacade,
		CurrencyFacade $currencyFacade,
		TransportPriceCalculation $transportPriceCalculation
	) {
		$this->em = $em;
		$this->transportRepository = $transportRepository;
		$this->paymentRepository = $paymentRepository;
		$this->transportVisibilityCalculation = $transportVisibilityCalculation;
		$this->domain = $domain;
		$this->imageFacade = $imageFacade;
		$this->currencyFacade = $currencyFacade;
		$this->transportPriceCalculation = $transportPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\TransportEditData $transportEditData
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function create(TransportEditData $transportEditData) {
		$transport = new Transport($transportEditData->transportData);
		$this->em->persist($transport);
		$this->em->flush();
		$this->updateTransportPrices($transport, $transportEditData->prices);
		$this->createTransportDomains($transport, $transportEditData->transportData->domains);
		$this->imageFacade->uploadImage($transport, $transportEditData->transportData->image, null);
		$this->em->flush();

		return $transport;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Transport\TransportEditData $transportEditData
	 */
	public function edit(Transport $transport, TransportEditData $transportEditData) {
		$transport->edit($transportEditData->transportData);

		$this->updateTransportPrices($transport, $transportEditData->prices);
		$this->deleteTransportDomainsByTransport($transport);
		$this->createTransportDomains($transport, $transportEditData->transportData->domains);
		$this->imageFacade->uploadImage($transport, $transportEditData->transportData->image, null);
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
		$this->deleteTransportDomainsByTransport($transport);
		$this->em->flush();
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
		return $this->getVisibleByDomainId($this->domain->getId(), $visiblePayments);
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $visiblePaymentsOnDomain
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getVisibleByDomainId($domainId, $visiblePaymentsOnDomain) {
		$transports = $this->transportRepository->getAllByDomainId($domainId);

		return $this->transportVisibilityCalculation->filterVisible($transports, $visiblePaymentsOnDomain, $domainId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @return \SS6\ShopBundle\Model\Transport\TransportDomain[]
	 */
	public function getTransportDomainsByTransport(Transport $transport) {
		return $this->transportRepository->getTransportDomainsByTransport($transport);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param string[currencyId] $prices
	 */
	private function updateTransportPrices(Transport $transport, $prices) {
		foreach ($this->currencyFacade->getAll() as $currency) {
			$price = $prices[$currency->getId()];
			$transport->setPrice($currency, $price);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport[]
	 */
	public function getAllIncludingDeleted() {
		return $this->transportRepository->getAllIncludingDeleted();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return string [transportId]
	 */
	public function getTransportPricesWithVatIndexedByTransportId(Currency $currency) {
		$transportPricesWithVatByTransportId = [];
		$transports = $this->getAllIncludingDeleted();
		foreach ($transports as $transport) {
			$transportPrice = $this->transportPriceCalculation->calculateIndependentPrice($transport, $currency);
			$transportPricesWithVatByTransportId[$transport->getId()] = $transportPrice->getPriceWithVat();
		}

		return $transportPricesWithVatByTransportId;
	}

	/**
	 * @return string[transportId]
	 */
	public function getTransportVatPercentsIndexedByTransportId() {
		$transportVatPercentsByTransportId = [];
		$transports = $this->getAllIncludingDeleted();
		foreach ($transports as $transport) {
			$transportVatPercentsByTransportId[$transport->getId()] = $transport->getVat()->getPercent();
		}

		return $transportVatPercentsByTransportId;
	}

}
