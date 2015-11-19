<?php

namespace SS6\ShopBundle\Component\Domain;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValue;

class DomainDataCreator {

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

	public function __construct(Domain $domain, Setting $setting, EntityManager $em) {
		$this->domain = $domain;
		$this->setting = $setting;
		$this->em = $em;
	}

	/**
	 * @return int
	 */
	public function createNewDomainsData() {
		$newDomainsCount = 0;
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainId = $domainConfig->getId();
			try {
				$this->setting->get(Setting::DOMAIN_DATA_CREATED, $domainId);
			} catch (\SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException $ex) {
				$domainDataCreatedSettingValue = new SettingValue(Setting::DOMAIN_DATA_CREATED, true, $domainId);
				$this->em->persist($domainDataCreatedSettingValue);
				$this->em->flush($domainDataCreatedSettingValue);
				$newDomainsCount++;
			}
		}

		return $newDomainsCount;
	}
}
