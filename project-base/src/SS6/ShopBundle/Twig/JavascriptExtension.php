<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Condition;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class JavascriptExtension extends Twig_Extension {

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var string
	 */
	private $webPath;

	/**
	 * @var array
	 */
	private $javascriptLinks;

	/**
	 * @param string $webPath
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function __construct($webPath, ContainerInterface $container) {
		$this->container = $container;
		$this->webPath = $webPath;
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
	 * @return array
	 */
	public function getFunctions() {
		return array(
			new Twig_SimpleFunction('importJavascripts', array($this, 'renderJavascripts'), array('is_safe' => array('html'))),
		);
	}

	/**
	 * @param string|array $javascripts
	 * @return string
	 */
	public function renderJavascripts($javascripts) {
		$javascripts = Condition::mixedToArray($javascripts);
		$this->javascriptLinks = [];
		
		foreach ($javascripts as $javascript) {
			$this->process($javascript);
		}

		$this->javascriptLinks = array_unique($this->javascriptLinks);

		return $this->getHtmlJavascriptImports($this->javascriptLinks);
	}

	/**
	 * @param array $javascriptLinks
	 * @return string
	 */
	private function getHtmlJavascriptImports(array $javascriptLinks) {
		$html = '';
		foreach ($javascriptLinks as $javascriptLink) {
			$html .= "\n" . '<script type="text/javascript" src="' . htmlspecialchars($javascriptLink, ENT_QUOTES) . '"></script>';
		}

		return $html;
	}

	/**
	 * @param string $relativeFilepath
	 * @return string
	 */
	private function getJavascriptFileUrl($relativeFilepath) {
		return $this->getAssetsHelper()->getUrl($relativeFilepath);
	}

	/**
	 * @param string $javascript
	 */
	private function process($javascript) {
		if ($this->processJavascriptFile($javascript)) {
			return;
		}
		if ($this->processJavascriptDirectoryMask($javascript)) {
			return;
		}

		$this->processExternalJavascripts($javascript);
	}

	/**
	 * @param string $javascript
	 * @return boolean
	 */
	private function processJavascriptFile($javascript) {
		$filepath = $this->webPath . '/' . $javascript;

		if (is_file($filepath)) {
			$this->javascriptLinks[] = $this->getJavascriptFileUrl($javascript);
			return true;
		}

		return false;
	}

	/**
	 * @param string $directoryMask
	 * @return boolean
	 */
	private function processJavascriptDirectoryMask($directoryMask) {
		$parts = explode('/', $directoryMask);
		$filenameMask = array_pop($parts);
		$path = implode('/', $parts);

		return $this->processJavascriptByMask($path, $filenameMask);
	}

	/**
	 * @param string $path
	 * @param string $filenameMask
	 * @return bool
	 */
	private function processJavascriptByMask($path, $filenameMask) {
		if ($filenameMask !== '' && strpos($filenameMask, '*') === false) {
			// non existing file still imports URL to generate 404
			return false;
		}

		$filesystemPath = $this->webPath . '/' . $path;

		if (is_dir($filesystemPath)) {
			$filenameMask = $filenameMask === '' ? '*' : $filenameMask;
			$filepaths = (array)glob($filesystemPath . '/' . $filenameMask);
			foreach ($filepaths as $filepath) {
				if (is_file($filepath)) {
					$filename = pathinfo($filepath, PATHINFO_BASENAME);
					$this->javascriptLinks[] = $this->getJavascriptFileUrl($path . '/' . $filename);
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * @param string $javascriptUrl
	 * @return boolean
	 */
	private function processExternalJavascripts($javascriptUrl) {
		$this->javascriptLinks[] = $javascriptUrl;
		return true;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return 'javascript_extension';
	}
}
