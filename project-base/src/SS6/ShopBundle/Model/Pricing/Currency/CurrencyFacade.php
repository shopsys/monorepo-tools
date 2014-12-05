<?php

namespace SS6\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyData;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyService;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository;

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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyRepository $currencyRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\CurrencyService $currencyService
	 */
	public function __construct(
		EntityManager $em,
		CurrencyRepository $currencyRepository,
		CurrencyService $currencyService
	) {
		$this->em = $em;
		$this->currencyRepository = $currencyRepository;
		$this->currencyService = $currencyService;
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
		$this->currencyService->edit($currency, $currencyData);
		$this->em->flush();

		return $currency;
	}

	/**
	 * @param int $currencyId
	 */
	public function deleteById($currencyId) {
		$currency = $this->currencyRepository->getById($currencyId);

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
		return $this->getById($this->currencyService->getDefaultCurrencyId());
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 */
	public function setDefaultCurrency(Currency $currency) {
		$this->currencyService->setDefaultCurrency($currency);
	}

}
