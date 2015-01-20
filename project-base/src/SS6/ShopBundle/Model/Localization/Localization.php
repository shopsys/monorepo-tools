<?php

namespace SS6\ShopBundle\Model\Localization;

use SS6\ShopBundle\Model\Domain\Domain;

class Localization {

	private $languageNames = [
		'cs' => 'Čeština',
		'de' => 'Deutsch',
		'en' => 'English',
		'hu' => 'Magyar',
		'pl' => 'Polski',
		'sk' => 'Slovenčina',
	];

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var array
	 */
	private $allLocales;

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Domain $domain
	 */
	public function __construct(Domain $domain) {
		$this->domain = $domain;
	}

	/**
	 * @return string
	 */
	public function getLocale() {
		return $this->domain->getLocale();
	}

	/**
	 * @return string
	 */
	public function getDefaultLocale() {
		return $this->domain->getDomainConfigById(1)->getLocale();
	}

	/**
	 * @return array
	 */
	public function getAllLocales() {
		if ($this->allLocales === null) {
			$this->allLocales = [];
			foreach ($this->domain->getAll() as $domainConfig) {
				$this->allLocales[$domainConfig->getLocale()] = $domainConfig->getLocale();
			}
		}

		return $this->allLocales;
	}

	public function getLanguageName($locale) {
		if (!array_key_exists($locale, $this->languageNames)) {
			throw new \SS6\ShopBundle\Model\Localization\Exception\InvalidLocaleException(
				sprintf('Locale "%s" is not valid', $locale)
			);
		}

		return $this->languageNames[$locale];
	}

}
