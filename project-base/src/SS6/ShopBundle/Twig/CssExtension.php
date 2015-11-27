<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Css\CssFacade;
use Twig_SimpleFunction;

class CssExtension extends \Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Component\Css\CssFacade
	 */
	private $cssFacade;

	public function __construct(CssFacade $cssFacade) {
		$this->cssFacade = $cssFacade;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction('getCssVersion', [$this, 'getCssVersion']),
		];
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'css';
	}

	/**
	 * @return string
	 */
	public function getCssVersion() {
		return $this->cssFacade->getCssVersion();
	}

}
