<?php

namespace SS6\ShopBundle\Twig\Javascript;

use SS6\ShopBundle\Component\Javascript\Compiler\JsCompiler;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class JavascriptCompilerService {

	const NOT_COMPILED_FOLDER = '/plugins/';
	const MAX_CACHE_TIME_WEEKS = 2;

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
	private $jsUrlPrefix;

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
	 * @var \SS6\ShopBundle\Component\Javascript\Compiler\JsCompiler
	 */
	private $jsCompiler;

	/**
	 * @var array
	 */
	private $javascriptLinks = [];

	public function __construct(
		$rootPath,
		$webPath,
		$jsSourcePath,
		$jsUrlPrefix,
		ContainerInterface $container,
		Filesystem $filesystem,
		Domain $domain,
		JsCompiler $jsCompiler
	) {
		$this->rootPath = $rootPath;
		$this->webPath = $webPath;
		$this->jsSourcePath = $jsSourcePath;
		$this->jsUrlPrefix = $jsUrlPrefix;
		$this->container = $container;
		$this->filesystem = $filesystem;
		$this->domain = $domain;
		$this->jsCompiler = $jsCompiler;
	}

	/**
	 * @param array $javascripts
	 */
	public function generateCompiledFiles(array $javascripts) {
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
		$sourcePath = $this->jsSourcePath . '/' . $javascript;
		$relativeTargetPath = $this->getRelativeTargetPath($javascript);

		if ($relativeTargetPath === null) {
			return false;
		}

		if (is_file($sourcePath)) {
			$lastModified = filemtime($sourcePath);
			$this->compileJavascriptFile($sourcePath, $relativeTargetPath);
			if ($lastModified < time() - self::MAX_CACHE_TIME_WEEKS * 7 * 24 * 3600) {
				$this->javascriptLinks[] = $this->getAssetsHelper()->getUrl($relativeTargetPath);
			} else {
				$this->javascriptLinks[] = $this->getAssetsHelper()->getUrl($relativeTargetPath) . '?v=' . $lastModified;
			}
			return true;
		}

		return false;
	}

	/**
	 * @param string $javascript
	 * @return string
	 */
	private function getRelativeTargetPath($javascript) {
		$relavitveTargetPath = null;
		if (strpos($javascript, 'admin/') === 0 || strpos($javascript, 'frontend/') === 0) {
			$relavitveTargetPath = substr($this->jsUrlPrefix, 1) . $javascript;
			if (strpos($relavitveTargetPath, '/') === 0) {
				$relavitveTargetPath = substr($relavitveTargetPath, 1);
			}

			$relavitveTargetPath = str_replace('/scripts/', '/scripts/' . $this->domain->getLocale() . '/', $relavitveTargetPath);
		}

		return $relavitveTargetPath;
	}

	/**
	 * @param string $sourceFilename
	 * @param string $relativeTargetPath
	 */
	private function compileJavascriptFile($sourceFilename, $relativeTargetPath) {
		$compiledFilename = $this->webPath . '/' . $relativeTargetPath;

		if (!$this->isCompiledFileFresh($compiledFilename, $sourceFilename)) {
			$content = file_get_contents($sourceFilename);

			if (strpos($sourceFilename, self::NOT_COMPILED_FOLDER) === false) {
				$newContent = $this->jsCompiler->compile($content);
			} else {
				$newContent = $content;
			}

			$this->filesystem->mkdir(dirname($compiledFilename));
			$this->filesystem->dumpFile($compiledFilename, $newContent);
		}
	}

	/**
	 * @param string $compiledFilename
	 * @param string $sourceFilename
	 * @return boolean
	 */
	private function isCompiledFileFresh($compiledFilename, $sourceFilename) {
		if (is_file($compiledFilename) && parse_url($sourceFilename, PHP_URL_HOST) === null) {
			$isCompiledFileFresh = filemtime($sourceFilename) < filemtime($compiledFilename);
		} else {
			$isCompiledFileFresh = false;
		}
		return $isCompiledFileFresh;
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
		$filesystemPath = $this->jsSourcePath . '/' . $path;

		if (is_dir($filesystemPath)) {
			$filepaths = (array)glob($filesystemPath . '/' . $filenameMask);
			foreach ($filepaths as $filepath) {
				$javascript = str_replace($this->jsSourcePath . '/', '', $filepath);
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
