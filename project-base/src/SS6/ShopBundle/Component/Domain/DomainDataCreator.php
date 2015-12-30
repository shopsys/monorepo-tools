<?php

namespace SS6\ShopBundle\Component\Domain;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use SS6\ShopBundle\Component\Setting\Setting;

class DomainDataCreator {

	const TEMPLATE_DOMAIN_ID = 1;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \Doctrine\ORM\EntityManager;
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator
	 */
	private $multidomainEntityDataCreator;

	public function __construct(
		Domain $domain,
		Setting $setting,
		EntityManager $em,
		MultidomainEntityDataCreator $multidomainEntityDataCreator
	) {
		$this->domain = $domain;
		$this->setting = $setting;
		$this->em = $em;
		$this->multidomainEntityDataCreator = $multidomainEntityDataCreator;
	}

	/**
	 * @return int
	 */
	public function createNewDomainsData() {
		$newDomainsCount = 0;
		foreach ($this->domain->getAllIncludingDomainConfigsWithoutDataCreated() as $domainConfig) {
			$domainId = $domainConfig->getId();
			try {
				$this->setting->get(Setting::DOMAIN_DATA_CREATED, $domainId);
			} catch (\SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException $ex) {
				$this->setting->copyAllMultidomainSettings(self::TEMPLATE_DOMAIN_ID, $domainId);
				$this->multidomainEntityDataCreator->copyAllMultidomainDataForNewDomain(self::TEMPLATE_DOMAIN_ID, $domainId);
				$newDomainsCount++;
			}
		}

		return $newDomainsCount;
	}
}
