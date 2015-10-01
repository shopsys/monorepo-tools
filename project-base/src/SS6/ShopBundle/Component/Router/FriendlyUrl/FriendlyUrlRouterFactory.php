<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGenerator;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlMatcher;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCompiler;

class FriendlyUrlRouterFactory {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader
	 */
	private $delegatingLoader;

	/**
	 * @var \Symfony\Component\Routing\RouteCompiler
	 */
	private $routeCompiler;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
	 */
	private $friendlyUrlRepository;

	/**
	 * @var string
	 */
	private $friendlyUrlRouterResourceFilepath;

	public function __construct(
		$friendlyUrlRouterResourceFilepath,
		DelegatingLoader $delegatingLoader,
		RouteCompiler $routeCompiler,
		FriendlyUrlRepository $friendlyUrlRepository
	) {
		$this->friendlyUrlRouterResourceFilepath = $friendlyUrlRouterResourceFilepath;
		$this->delegatingLoader = $delegatingLoader;
		$this->routeCompiler = $routeCompiler;
		$this->friendlyUrlRepository = $friendlyUrlRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param \Symfony\Component\Routing\RequestContext $context
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter
	 */
	public function createRouter(DomainConfig $domainConfig, RequestContext $context) {
		return new FriendlyUrlRouter(
			$context,
			$this->delegatingLoader,
			new FriendlyUrlGenerator($context, $this->routeCompiler, $this->friendlyUrlRepository),
			new FriendlyUrlMatcher($this->friendlyUrlRepository),
			$domainConfig,
			$this->friendlyUrlRouterResourceFilepath
		);
	}

}
