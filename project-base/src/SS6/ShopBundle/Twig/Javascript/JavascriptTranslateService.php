<?php

namespace SS6\ShopBundle\Twig\Javascript;

use SS6\ShopBundle\Component\Translation\JsTranslator;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class JavascriptTranslateService {

	const NOT_TRANSLATED_FOLDER = '/plugins/';

	/**
	 * @var string
	 */
	private $rootPath;

	/**
	 * @var string
	 */
	private $webPath;

	/**
	 * @var string
	 */
	private $jsTargetPath;

	/**
	 * @var string
	 */
	private $jsSourcePath;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var SS6\ShopBundle\Component\Translation\JsTranslator
	 */
	private $jsTranslator;

	/**
	 * @var array
	 */
	private $javascriptLinks = [];

	public function __construct(
		$rootPath,
		$webPath,
		$jsSourcePath,
		$jsTargetPath,
		ContainerInterface $container,
		Filesystem $filesystem,
		Domain $domain,
		JsTranslator $jsTranslator
	) {
		$this->rootPath = $rootPath;
		$this->webPath = $webPath;
		$this->jsSourcePath = $jsSourcePath;
		$this->jsTargetPath = $jsTargetPath;
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->domain = $domain;
		$this->jsTranslator = $jsTranslator;
	}

	/**
	 * @param array $javascripts
	 */
	public function generateTranslateFiles(array $javascripts) {
		foreach ($javascripts as $javascript) {
			$this->process($javascript);
		}

		$this->javascriptLinks = array_unique($this->javascriptLinks);
	}

	/**
	 * @return string[]
	 */
	public function getGeneratedLinks() {
		return $this->javascriptLinks;
	}

	/**
	 * Service "templating.helper.assets" cannot be created in CLI, because service "request" is inactive in CLI
	 *
	 * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper
	 */
	private function getAssetsHelper() {
		return $this->container->get('templating.helper.assets');
	}

	/**
	 * @param string $javascript
	 */
	private function process($javascript) {
		if ($this->tryToProcessJavascriptFile($javascript)) {
			return;
		}

		if ($this->tryToProcessJavascriptDirectoryMask($javascript)) {
			return;
		}

		$this->processExternalJavascript($javascript);
	}

	/**
	 * @param string $javascript
	 * @return boolean
	 */
	private function tryToProcessJavascriptFile($javascript) {
		$sourcePath = $this->jsSourcePath . $javascript;
		$targetPath = $this->getTargetPath($javascript);

		if ($targetPath === null) {
			return false;
		}

		if (is_file($sourcePath)) {
			$this->translateJavascriptIntoCacheFile($sourcePath, $targetPath);
			$this->javascriptLinks[] = $this->getAssetsHelper()->getUrl($targetPath);
			return true;
		}

		return false;
	}

	/**
	 * @param string $javascript
	 * @return string
	 */
	private function getTargetPath($javascript) {
		$targetPath = null;
		if (strpos($javascript, 'admin/') === 0 || strpos($javascript, 'frontend/') === 0) {
			$targetPath = $this->jsTargetPath . $javascript;
			$targetPath = str_replace('/scripts/', '/scripts/' . $this->domain->getLocale() . '/', $targetPath);
		}

		return $targetPath;
	}

	/**
	 * @param string $filename
	 * @param string $cachedPath
	 */
	private function translateJavascriptIntoCacheFile($filename, $cachedPath) {
		$cachedPathFull = $this->webPath . $cachedPath;

		if (is_file($cachedPathFull) && parse_url($filename, PHP_URL_HOST) === null) {
			$doCopy = filemtime($filename) > filemtime($cachedPathFull);
		} else {
			$doCopy = true;
		}

		if ($doCopy) {
			$content = file_get_contents($filename);

			if (strpos($filename, self::NOT_TRANSLATED_FOLDER) === false) {
				$newContent = $this->jsTranslator->translate($content);
			} else {
				$newContent = $content;
			}

			$this->filesystem->mkdir(dirname($cachedPathFull));
			$this->filesystem->dumpFile($cachedPathFull, $newContent);
		}
	}

	/**
	 * @param string $directoryMask
	 * @return boolean
	 */
	private function tryToProcessJavascriptDirectoryMask($directoryMask) {
		$parts = explode('/', $directoryMask);
		$mask = array_pop($parts);
		$path = implode('/', $parts);

		if (!$this->isMaskValid($mask)) {
			return false;
		}

		$filenameMask = $mask === '' ? '*' : $mask;

		return $this->processJavascriptByMask($path, $filenameMask);
	}

	/**
	 * @param string $path
	 * @param string $filenameMask
	 * @return bool
	 */
	private function processJavascriptByMask($path, $filenameMask) {
		$filesystemPath = $this->jsSourcePath . $path;

		if (is_dir($filesystemPath)) {
			$filepaths = (array)glob($filesystemPath . '/' . $filenameMask);
			foreach ($filepaths as $filepath) {
				$javascript = str_replace($this->jsSourcePath, '', $filepath);
				$this->tryToProcessJavascriptFile($javascript);
			}
		}

		return true;
	}

	/**
	 * @param string $filenameMask
	 * @return boolean
	 */
	private function isMaskValid($filenameMask) {
		return $filenameMask === '' || strpos($filenameMask, '*') !== false;
	}

	/**
	 * @param string $javascriptUrl
	 */
	private function processExternalJavascript($javascriptUrl) {
		$this->javascriptLinks[] = $this->getAssetsHelper()->getUrl($javascriptUrl);
	}

}