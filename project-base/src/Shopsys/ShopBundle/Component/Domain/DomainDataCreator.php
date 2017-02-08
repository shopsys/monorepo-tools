<?php

namespace SS6\ShopBundle\Component\Domain;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Component\Setting\SettingValueRepository;
use SS6\ShopBundle\Component\Translation\TranslatableEntityDataCreator;

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
	 * @var \SS6\ShopBundle\Component\Setting\SettingValueRepository
	 */
	private $settingValueRepository;

	/**
	 * @var \Doctrine\ORM\EntityManager;
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityDataCreator
	 */
	private $multidomainEntityDataCreator;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\TranslatableEntityDataCreator
	 */
	private $translatableEntityDataCreator;

	public function __construct(
		Domain $domain,
		Setting $setting,
		SettingValueRepository $settingValueRepository,
		EntityManager $em,
		MultidomainEntityDataCreator $multidomainEntityDataCreator,
		TranslatableEntityDataCreator $translatableEntityDataCreator
	) {
		$this->domain = $domain;
		$this->setting = $setting;
		$this->settingValueRepository = $settingValueRepository;
		$this->em = $em;
		$this->multidomainEntityDataCreator = $multidomainEntityDataCreator;
		$this->translatableEntityDataCreator = $translatableEntityDataCreator;
	}

	/**
	 * @return int
	 */
	public function createNewDomainsData() {
		$newDomainsCount = 0;
		foreach ($this->domain->getAllIncludingDomainConfigsWithoutDataCreated() as $domainConfig) {
			$domainId = $domainConfig->getId();
			try {
				$this->setting->getForDomain(Setting::DOMAIN_DATA_CREATED, $domainId);
			} catch (\SS6\ShopBundle\Component\Setting\Exception\SettingValueNotFoundException $ex) {
				$this->settingValueRepository->copyAllMultidomainSettings(self::TEMPLATE_DOMAIN_ID, $domainId);
				$this->setting->clearCache();
				$this->multidomainEntityDataCreator->copyAllMultidomainDataForNewDomain(self::TEMPLATE_DOMAIN_ID, $domainId);
				$locale = $domainConfig->getLocale();
				if ($this->isNewLocale($locale)) {
					$this->translatableEntityDataCreator->copyAllTranslatableDataForNewLocale(
						$this->getTemplateLocale(),
						$locale
					);
				}
				$newDomainsCount++;
			}
		}

		return $newDomainsCount;
	}

	/**
	 * @param string $locale
	 * @return bool
	 */
	private function isNewLocale($locale) {
		foreach ($this->domain->getAll() as $domainConfig) {
			if ($domainConfig->getLocale() === $locale) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return string
	 */
	private function getTemplateLocale() {
		return $this->domain->getDomainConfigById(self::TEMPLATE_DOMAIN_ID)->getLocale();
	}
}
