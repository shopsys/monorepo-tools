<?php

namespace SS6\ShopBundle\Model\Pricing\Currency;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;

class CurrencyRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getCurrencyRepository() {
		return $this->em->getRepository(Currency::class);
	}

	/**
	 * @param int $currencyId
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency|null
	 */
	public function findById($currencyId) {
		return $this->getCurrencyRepository()->find($currencyId);
	}

	/**
	 * @param int $currencyId
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency
	 */
	public function getById($currencyId) {
		$currency = $this->findById($currencyId);

		if ($currency === null) {
			throw new \SS6\ShopBundle\Model\Pricing\Currency\Exception\CurrencyNotFoundException($currencyId);
		}

		return $currency;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	public function getAll() {
		return $this->getCurrencyRepository()->findAll();
	}
	
}
