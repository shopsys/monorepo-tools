<?php

namespace SS6\ShopBundle\Model\Localization\Translation;

use JMS\TranslationBundle\Model\Message as JmsMessage;
use JMS\TranslationBundle\Model\MessageCatalogue as JmsMessageCatalogue;
use SS6\ShopBundle\Component\Translation\PoDumper;
use SS6\ShopBundle\Component\Translation\PoFileLoader;
use SS6\ShopBundle\Component\Translation\Translator;
use SS6\ShopBundle\Model\Localization\Localization;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

class TranslationEditFacade {

	/**
	 * @var string
	 */
	private $rootDir;

	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\PoFileLoader
	 */
	private $poFileLoader;

	/**
	 * @var \SS6\ShopBundle\Component\Translation\PoDumper
	 */
	private $poDumper;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		$rootDir,
		$cacheDir,
		Filesystem $filesystem,
		PoFileLoader $poFileLoader,
		PoDumper $poDumper,
		Localization $localization
	) {
		$this->rootDir = $rootDir;
		$this->cacheDir = $cacheDir;
		$this->filesystem = $filesystem;
		$this->poFileLoader = $poFileLoader;
		$this->poDumper = $poDumper;
		$this->localization = $localization;
	}

	/**
	 * @param string $translationId
	 * @param array $translation
	 */
	public function saveTranslation($translationId, $translation) {
		foreach ($this->getTranslatableLocales() as $locale) {
			if (array_key_exists($locale, $translation)) {
				$catalogue = $this->loadCatalogueFromFile(Translator::DEFAULT_DOMAIN, $locale);
				if ($catalogue->has($translationId, Translator::DEFAULT_DOMAIN)) {
					$catalogue->set($translationId, $translation[$locale], Translator::DEFAULT_DOMAIN);
					$this->dumpCatalogToFile($catalogue, Translator::DEFAULT_DOMAIN, $locale);
				}
			}
		}

		$this->invalidateTranslationCache();
	}

	private function invalidateTranslationCache() {
		$finder = new Finder();
		$finder->in($this->cacheDir . '/translations');
		$this->filesystem->remove($finder->files());
	}

	/**
	 * @param string $domain
	 * @param string $locale
	 * @return string
	 */
	private function getResourceFilepath($domain, $locale) {
		return $this->rootDir
			. '/../src/SS6/ShopBundle/Resources/translations/custom/'
			. $domain . '.' . $locale . '.po';
	}

	/**
	 * @param string $domain
	 * @param string $locale
	 * @return \Symfony\Component\Translation\MessageCatalogue
	 */
	private function loadCatalogueFromFile($domain, $locale) {
		$filename = $this->getResourceFilepath($domain, $locale);

		if (file_exists($filename)) {
			$catalogue = $this->poFileLoader->loadIncludingEmpty($filename, $locale, $domain);
		} else {
			$catalogue = new MessageCatalogue($locale);
		}

		return $catalogue;
	}

	/**
	 * @param \Symfony\Component\Translation\MessageCatalogue $catalogue
	 * @param string $domain
	 * @param string $locale
	 */
	private function dumpCatalogToFile(MessageCatalogue $catalogue, $domain, $locale) {
		$jmsMessageCatalogue = new JmsMessageCatalogue();
		$jmsMessageCatalogue->setLocale($locale);
		foreach ($catalogue->all($domain) as $key => $translation) {
			$jmsMessage = new JmsMessage($key, $domain);
			$jmsMessage->setLocaleString($translation);
			$jmsMessage->setNew(false);
			$jmsMessageCatalogue->add($jmsMessage);
		}

		file_put_contents(
			$this->getResourceFilepath($domain, $locale),
			$this->poDumper->dump($jmsMessageCatalogue, $domain)
		);
	}

	/**
	 * @param string $translationId
	 * @return string[locale]
	 */
	public function getTranslationById($translationId) {
		$translationsData = $this->getAllTranslationsData();

		$translationData = [];
		foreach ($this->getTranslatableLocales() as $locale) {
			if (isset($translationsData[$translationId][$locale])) {
				$translationData[$locale] = $translationsData[$translationId][$locale];
			} else {
				$translationData[$locale] = null;
			}
		}

		return $translationData;
	}

	/**
	 * @return string[translationId][locale]
	 */
	public function getAllTranslationsData() {
		$catalogues = [];
		foreach ($this->getTranslatableLocales() as $locale) {
			$catalogues[$locale] = $this->loadCatalogueFromFile(Translator::DEFAULT_DOMAIN, $locale);
		}

		$data = [];

		foreach ($catalogues as $locale => $catalogue) {
			foreach ($catalogue->all(Translator::DEFAULT_DOMAIN) as $id => $translation) {
				$data[$id]['id'] = $id;
				$data[$id][$locale] = $translation;
			}
		}

		return $data;
	}

	/**
	 * @return string[]
	 */
	public function getTranslatableLocales() {
		$translatableLocales = [];
		foreach ($this->localization->getAllLocales() as $locale) {
			if ($locale !== Translator::SOURCE_LOCALE) {
				$translatableLocales[] = $locale;
			}
		}

		return $translatableLocales;
	}

}
