<?php

namespace SS6\ShopBundle\Component;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator {

	const DEFAULT_DOMAIN = 'messages';
	const NOT_TRANSLATED_PREFIX = '##';

	/**
	 * @param string $locale
	 * @return \Symfony\Component\Translation\MessageCatalogue
	 */
	public function getCatalogue($locale) {
		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		return $this->catalogues[$locale];
	}

	/**
	 * When translation for given locale is not defined,
	 * function returns translation id string with self::NOT_TRANSLATED_PREFIX prefix
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
		if (null === $locale) {
			$locale = $this->getLocale();
		} else {
			$this->assertValidLocale($locale);
		}

		if (null === $domain) {
			$domain = 'messages';
		}

		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		$catalogue = $this->catalogues[$locale];
		if ($catalogue->defines($id, $domain)) {
			return strtr($this->catalogues[$locale]->get((string)$id, $domain), $parameters);
		} else {
			return self::NOT_TRANSLATED_PREFIX . $id;
		}
	}

	/**
	 * When translation for given locale is not defined,
	 * function returns translation id string with self::NOT_TRANSLATED_PREFIX prefix
	 * {@inheritdoc}
	 *
	 * @api
	 */
	public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
		if (null === $locale) {
			$locale = $this->getLocale();
		} else {
			$this->assertValidLocale($locale);
		}

		if (null === $domain) {
			$domain = 'messages';
		}

		if (!isset($this->catalogues[$locale])) {
			$this->loadCatalogue($locale);
		}

		$id = (string)$id;

		$catalogue = $this->catalogues[$locale];
		while (!$catalogue->defines($id, $domain)) {
			if ($catalogue->getFallbackCatalogue()) {
				$cat = $catalogue->getFallbackCatalogue();
				$catalogue = $cat;
				$locale = $catalogue->getLocale();
			} else {
				break;
			}
		}

		if ($catalogue->defines($id, $domain)) {
			return strtr($this->selector->choose($catalogue->get($id, $domain), (int)$number, $locale), $parameters);
		} else {
			return self::NOT_TRANSLATED_PREFIX . $id;
		}
	}

}
