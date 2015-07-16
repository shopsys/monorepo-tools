<?php

namespace SS6\ShopBundle\Model\Domain;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Image\ImageService;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\PricingSetting;

class DomainFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\PricingSetting
	 */
	private $pricingSetting;

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageService
	 */
	private $imageService;

	public function __construct(
		Domain $domain,
		PricingSetting $pricingSetting,
		ImageService $imageService
	) {
		$this->domain = $domain;
		$this->pricingSetting = $pricingSetting;
		$this->imageService = $imageService;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Currency\Currency $currency
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	public function getDomainConfigsByCurrency(Currency $currency) {
		$domainConfigs = [];
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainCurrencyId = $this->pricingSetting->getDomainDefaultCurrencyIdByDomainId($domainConfig->getId());
			if ($domainCurrencyId === $currency->getId()) {
				$domainConfigs[] = $domainConfig;
			}
		}

		return $domainConfigs;
	}

	/**
	 * @param int $domainId
	 * @param string $iconName
	 */
	public function editIcon($domainId, $iconName) {
		$this->imageService->editDomainIcon($domainId, $iconName);
	}

}
