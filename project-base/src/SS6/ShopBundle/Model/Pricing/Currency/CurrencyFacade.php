<?php

namespace SS6\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyService;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository $currencyRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyService $currencyService
	 */
	public function __construct(
		EntityManager $em,
		CurrencyRepository $currencyRepository,
		CurrencyService $currencyService,
		PricingSetting $pricingSetting
	) {
		$this->em = $em;
		$this->currencyRepository = $currencyRepository;
		$this->currencyService = $currencyService;
		$this->pricingSetting = $pricingSetting;
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

		if ($this->currencyService->isCurrencyNotAllowedToDelete($currency)) {
			throw new \SS6\ShopBundle\Model\Pricing\Currency\Exception\DeletingDefaultCurrencyException();
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
		$this->currencyService->setDefaultCurrency($currency);
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
	 * @return array
	 */
	public function getNotAllowedToDeleteCurrencyIds() {
		return $this->currencyService->getNotAllowedToDeleteCurrencyIds();
	}

}
