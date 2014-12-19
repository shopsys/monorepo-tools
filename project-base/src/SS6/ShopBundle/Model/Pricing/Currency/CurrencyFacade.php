<?php

namespace SS6\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Order\OrderRepository;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyService;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class CurrencyFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository
	 */
	private $currencyRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Currency\CurrencyService
	 */
	private $currencyService;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository $currencyRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyService $currencyService
	 */
	public function __construct(
		EntityManager $em,
		CurrencyRepository $currencyRepository,
		CurrencyService $currencyService,
		PricingSetting $pricingSetting,
		OrderRepository $orderRepository,
		Domain $domain
	) {
		$this->em = $em;
		$this->currencyRepository = $currencyRepository;
		$this->currencyService = $currencyService;
		$this->pricingSetting = $pricingSetting;
		$this->orderRepository = $orderRepository;
		$this->domain = $domain;
	}

	/**
	 * @param int $currencyId
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getById($currencyId) {
		return $this->currencyRepository->getById($currencyId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function create(CurrencyData $currencyData) {
		$currency = $this->currencyService->create($currencyData);
		$this->em->persist($currency);
		$this->em->flush();

		return $currency;
	}

	/**
	 * @param int $currencyId
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyData $currencyData
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function edit($currencyId, CurrencyData $currencyData) {
		$currency = $this->currencyRepository->getById($currencyId);
		$this->currencyService->edit($currency, $currencyData, $this->isDefaultCurrency($currency));
		$this->em->flush();

		return $currency;
	}

	/**
	 * @param int $currencyId
	 */
	public function deleteById($currencyId) {
		$currency = $this->currencyRepository->getById($currencyId);

		if (in_array($currency->getId(), $this->getNotAllowedToDeleteCurrencyIds())) {
			throw new \SS6\ShopBundle\Model\Pricing\Currency\Exception\DeletingNotAllowedToDeleteCurrencyException();
		}
		$this->em->beginTransaction();

		$this->em->remove($currency);
		$this->em->flush();
		$this->em->commit();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	public function getAll() {
		return $this->currencyRepository->getAll();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getDefaultCurrency() {
		return $this->getById($this->pricingSetting->getDefaultCurrencyId());
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getDomainDefaultCurrencyByDomainId($domainId) {
		return $this->getById($this->pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainId));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	public function setDefaultCurrency(Currency $currency) {
		$this->pricingSetting->setDefaultCurrency($currency);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @param int $domainId
	 */
	public function setDomainDefaultCurrency(Currency $currency, $domainId) {
		$this->pricingSetting->setDomainDefaultCurrency($currency, $domainId);
	}

	/**
	 * @return int[]
	 */
	public function getNotAllowedToDeleteCurrencyIds() {
		return $this->currencyService->getNotAllowedToDeleteCurrencyIds(
			$this->getDefaultCurrency()->getId(),
			$this->getCurrenciesUsedInOrders(),
			$this->pricingSetting,
			$this->domain
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return bool
	 */
	public function isDefaultCurrency(Currency $currency) {
		return $currency === $this->getDefaultCurrency();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	public function getCurrenciesUsedInOrders() {
		return $this->orderRepository->getCurrenciesUsedInOrders();
	}

}
