<?php

namespace SS6\ShopBundle\Model\Localize;

use SS6\ShopBundle\Model\Domain\Domain;

class Localize {

	private $languageNames = array(
		'cs' => 'Čeština',
		'de' => 'Deutsch',
		'en' => 'English',
		'hu' => 'Magyar',
		'pl' => 'Polski',
		'sk' => 'Slovenčina',
	);

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
			$this->allLocales = array();
			foreach ($this->domain->getAll() as $domainConfig) {
				$this->allLocales[$domainConfig->getLocale()] = $domainConfig->getLocale();
			}
		}

		return $this->allLocales;
	}

	public function getLanguageName($locale) {
		if (!array_key_exists($locale, $this->languageNames)) {
			throw new \SS6\ShopBundle\Model\Localize\Exception\InvalidLocaleException();
		}

		return $this->languageNames[$locale];
	}

}
