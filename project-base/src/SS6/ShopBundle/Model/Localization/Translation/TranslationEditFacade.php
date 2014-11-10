<?php

namespace SS6\ShopBundle\Model\Localization\Translation;

use SS6\ShopBundle\Component\Translator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Dumper;

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
	 * @var \SS6\ShopBundle\Component\Translator
	 */
	private $translator;

	public function __construct($rootDir, $cacheDir, Translator $translator, Filesystem $filesystem) {
		$this->rootDir = $rootDir;
		$this->cacheDir = $cacheDir;
		$this->translator = $translator;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param string $translationId
	 * @param array $translation
	 */
	public function saveTranslation($translationId, $translation) {
		foreach ($translation as $locale => $translationText) {
			$catalogue = $this->translator->getCatalogue($locale);
			$catalogue->add(array($translationId => $translationText), Translator::DEFAULT_DOMAIN);
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
			. $domain . '.' . $locale . '.yml';

		$ymlDumper = new Dumper();
		file_put_contents($filepath, $ymlDumper->dump($catalogue, 1));
	}

	/**
	 * @param string $translationId
	 * @return array
	 */
	public function getTranslationById($translationId) {
		$translationData = array(
			'cs' => $this->translator->getCatalogue('cs')->get($translationId, Translator::DEFAULT_DOMAIN),
			'en' => $this->translator->getCatalogue('en')->get($translationId, Translator::DEFAULT_DOMAIN),
		);

		return $translationData;
	}

	/**
	 * @return array
	 */
	public function getAllTranslations() {
		$catalogueCs = $this->translator->getCatalogue('cs');
		$catalogueEn = $this->translator->getCatalogue('en');

		$data = array();
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
