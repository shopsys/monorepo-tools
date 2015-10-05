<?php

namespace SS6\ShopBundle\Component\Translation;

use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class Translator implements TranslatorInterface, TranslatorBagInterface {

	const DEFAULT_DOMAIN = 'messages';
	const NOT_TRANSLATED_PREFIX = '##';
	const SOURCE_LOCALE = 'cs';

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	private $originalTranslator;

	/**
	 * @var \Symfony\Component\Translation\TranslatorBagInterface
	 */
	private $originalTranslatorBag;

	/**
	 * @var \Symfony\Component\Translation\MessageSelector
	 */
	private $messageSelector;

	public function __construct(
		TranslatorInterface $originalTranslator,
		TranslatorBagInterface $originalTranslatorBag,
		MessageSelector $messageSelector
	) {
		$this->originalTranslator = $originalTranslator;
		$this->originalTranslatorBag = $originalTranslatorBag;
		$this->messageSelector = $messageSelector;
	}

	/**
	 * When translation for given locale is not defined and locale is not self::SOURCE_LOCALE,
	 * function returns translation ID string with self::NOT_TRANSLATED_PREFIX prefix.
	 * {@inheritdoc}
	 */
	public function trans($id, array $parameters = [], $domain = null, $locale = null) {
		$resolvedLocale = $this->resolveLocale($locale);
		$resolvedDomain = $this->resolveDomain($domain);

		if ($resolvedLocale === self::SOURCE_LOCALE) {
			return strtr($id, $parameters);
		}

		$message = $this->originalTranslator->trans($id, $parameters, $resolvedDomain, $resolvedLocale);
		$catalogue = $this->originalTranslatorBag->getCatalogue($resolvedLocale);

		if ($catalogue->defines($id, $resolvedDomain)) {
			return $message;
		} else {
			return self::NOT_TRANSLATED_PREFIX . $message;
		}
	}

	/**
	 * When translation for given locale is not defined and locale is not self::SOURCE_LOCALE,
	 * function returns translation ID string with self::NOT_TRANSLATED_PREFIX prefix.
	 * {@inheritdoc}
	 */
	public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null) {
		$resolvedLocale = $this->resolveLocale($locale);
		$resolvedDomain = $this->resolveDomain($domain);

		if ($resolvedLocale === self::SOURCE_LOCALE) {
			$message = $this->messageSelector->choose($id, (int)$number, $resolvedLocale);
			return strtr($message, $parameters);
		}

		$message = $this->originalTranslator->transChoice($id, $number, $parameters, $resolvedDomain, $resolvedLocale);
		$catalogue = $this->originalTranslatorBag->getCatalogue($resolvedLocale);

		if ($catalogue->defines($id, $resolvedDomain)) {
			return $message;
		} else {
			return self::NOT_TRANSLATED_PREFIX . $message;
		}
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	private function resolveLocale($locale) {
		if ($locale === null) {
			return $this->getLocale();
		}

		return $locale;
	}

	/**
	 * @param string|null $domain
	 * @return string
	 */
	private function resolveDomain($domain) {
		if ($domain === null) {
			return self::DEFAULT_DOMAIN;
		}

		return $domain;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLocale() {
		return $this->originalTranslator->getLocale();
	}

	/**
	 * {@inheritDoc}
	 */
	public function setLocale($locale) {
		$this->originalTranslator->setLocale($locale);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCatalogue($locale = null) {
		return $this->originalTranslatorBag->getCatalogue($locale);
	}

}
