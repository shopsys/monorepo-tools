<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGenerator;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlMatcher;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;

class FriendlyUrlRouterFactory {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader
	 */
	private $delegatingLoader;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGenerator
	 */
	private $urlGenerator;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlMatcher
	 */
	private $urlMatcher;

	/**
	 * @var array
	 */
	private $friendlyUrlRouterConfiguration;

	public function __construct(
		$localeRoutersConfiguration,
		DelegatingLoader $delegatingLoader,
		FriendlyUrlGenerator $urlGenerator,
		FriendlyUrlMatcher $urlMatcher
	) {
		$this->friendlyUrlRouterConfiguration = $localeRoutersConfiguration;
		$this->delegatingLoader = $delegatingLoader;
		$this->urlGenerator = $urlGenerator;
		$this->urlMatcher = $urlMatcher;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param \Symfony\Component\Routing\RequestContext $context
	 * @return \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter
	 */
	public function createRouter(DomainConfig $domainConfig, RequestContext $context) {
		return new FriendlyUrlRouter(
			$context,
			$this->delegatingLoader,
			$this->urlGenerator,
			$this->urlMatcher,
			$domainConfig,
			$this->friendlyUrlRouterConfiguration
		);
	}

}
