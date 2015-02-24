<?php

namespace SS6\ShopBundle\Twig;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use Twig_Extension;
use Twig_SimpleFunction;

class RouterExtension extends Twig_Extension {

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @param \SS6\ShopBundle\Component\Router\DomainRouterFactory $domainRouterFactory
	 */
	public function __construct(DomainRouterFactory $domainRouterFactory) {
		$this->domainRouterFactory = $domainRouterFactory;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		return [
			new Twig_SimpleFunction(
				'getUrlByDomainId',
				[$this, 'getUrlByDomainId']
			),
		];
	}

	/**
	 * @param string $route
	 * @param array $routeParams
	 * @param int $domainId
	 * @param bool $absolute
	 * @return string
	 */
	public function getUrlByDomainId($route, $routeParams, $domainId, $absolute) {
		$domainRouter = $this->domainRouterFactory->getRouter($domainId);

		return $domainRouter->generate($route, $routeParams, $absolute);
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'router_extension';
	}
}
