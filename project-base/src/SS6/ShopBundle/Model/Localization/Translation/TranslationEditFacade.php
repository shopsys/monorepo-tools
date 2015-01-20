<?php

namespace SS6\ShopBundle\Model\Localization\Translation;

use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use SS6\ShopBundle\Component\Translation\PoDumper;
use SS6\ShopBundle\Component\Translation\Translator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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
	 * @var \SS6\ShopBundle\Component\Translation\Translator
	 */
	private $translator;

	/**
	 * @var SS6\ShopBundle\Component\Translation\PoDumper
	 */
	private $poDumper;

	public function __construct($rootDir, $cacheDir, Translator $translator, Filesystem $filesystem, PoDumper $poDumper) {
		$this->rootDir = $rootDir;
		$this->cacheDir = $cacheDir;
		$this->translator = $translator;
		$this->filesystem = $filesystem;
		$this->poDumper = $poDumper;
	}

	/**
	 * @param string $translationId
	 * @param array $translation
	 */
	public function saveTranslation($translationId, $translation) {
		foreach ($translation as $locale => $translationText) {
			$catalogue = $this->translator->getCatalogue($locale);
			$catalogue->add([$translationId => $translationText], Translator::DEFAULT_DOMAIN);
			$this->dumpCatalogToFile($catalogue->all(Translator::DEFAULT_DOMAIN), Translator::DEFAULT_DOMAIN, $locale);
		}

		$this->invalidateTranslationCache();
	}

	private function invalidateTranslationCache() {
		$finder = new Finder();
		$finder->in($this->cacheDir . '/translations');
		$this->filesystem->remove($finder->files());
	}

	/**
	 * @param array $catalogue
	 * @param string $domain
	 * @param string $locale
	 */
	private function dumpCatalogToFile($catalogue, $domain, $locale) {
		$filepath = $this->rootDir
			. '/../src/SS6/ShopBundle/Resources/translations/'
			. $domain . '.' . $locale . '.po';

		$messageCatalogue = new MessageCatalogue();
		$messageCatalogue->setLocale($locale);
		foreach ($catalogue as $key => $translation) {
			$message = new Message($key, $domain);
			$message->setLocaleString($translation);
			$message->setNew(false);
			$messageCatalogue->add($message);
		}

		file_put_contents($filepath, $this->poDumper->dump($messageCatalogue, $domain));
	}

	/**
	 * @param string $translationId
	 * @return array
	 */
	public function getTranslationById($translationId) {
		$translationData = [
			'cs' => $this->translator->getCatalogue('cs')->get($translationId, Translator::DEFAULT_DOMAIN),
			'en' => $this->translator->getCatalogue('en')->get($translationId, Translator::DEFAULT_DOMAIN),
		];

		return $translationData;
	}

	/**
	 * @return array
	 */
	public function getAllTranslations() {
		$catalogueCs = $this->translator->getCatalogue('cs');
		$catalogueEn = $this->translator->getCatalogue('en');

		$data = [];
		foreach ($catalogueCs->all(Translator::DEFAULT_DOMAIN) as $id => $translation) {
			$data[$id]['id'] = $id;
			$data[$id]['cs'] = $translation;
			$data[$id]['en'] = null;
		}

		foreach ($catalogueEn->all(Translator::DEFAULT_DOMAIN) as $id => $translation) {
			$data[$id]['id'] = $id;
			if (!isset($data[$id]['cs'])) {
				$data[$id]['cs'] = null;
			}
			$data[$id]['en'] = $translation;
		}

		return $data;
	}
}
