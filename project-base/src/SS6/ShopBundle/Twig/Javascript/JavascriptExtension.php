<?php

namespace SS6\ShopBundle\Twig\Javascript;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Twig\Javascript\JavascriptTranslateService;
use Twig_Extension;
use Twig_SimpleFunction;

class JavascriptExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Twig\Javascript\JavascriptTranslateService
	 */
	private $javascriptTranslateService;

	public function __construct(JavascriptTranslateService $javascriptTranslateService) {
		$this->javascriptTranslateService = $javascriptTranslateService;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('importJavascripts', [$this, 'renderJavascripts'], ['is_safe' => ['html']]),
		];
	}

	/**
	 * @param string|array $javascripts
	 * @return string
	 */
	public function renderJavascripts($javascripts) {
		$javascriptsArray = Condition::mixedToArray($javascripts);

		$this->javascriptTranslateService->generateTranslateFiles($javascriptsArray);
		$javascriptLinks = $this->javascriptTranslateService->getGeneratedLinks();

		return $this->getHtmlJavascriptImports($javascriptLinks);
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
	 * @return string
	 */
	public function getName() {
		return 'javascript_extension';
	}
}
