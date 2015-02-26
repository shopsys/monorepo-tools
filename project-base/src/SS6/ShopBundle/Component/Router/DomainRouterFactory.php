<?php

namespace SS6\ShopBundle\Component\Router;

use SS6\ShopBundle\Component\Router\LocalizedRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class DomainRouterFactory {

	const URL_SCHEME = 'scheme';
	const URL_SCHEME_HTTP = 'http';
	const URL_SCHEME_HTTPS = 'https';
	const URL_HOST = 'host';
	const URL_PORT = 'port';

	/**
	 * @var \SS6\ShopBundle\Component\Router\LocalizedRouterFactory
	 */
	private $localizedRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Symfony\Component\Config\Loader\DelegatingLoader
	 */
	private $delegatingLoader;

	/**
	 * @var string
	 */
	private $routerConfiguration;

	/**
	 * @var \Symfony\Component\Routing\Router[]
	 */
	private $routersByDomainId = [];

	public function __construct(
		$routerConfiguration,
		DelegatingLoader $delegatingLoader,
		LocalizedRouterFactory $localizedRouterFactory,
		Domain $domain
	) {
		$this->routerConfiguration = $routerConfiguration;
		$this->delegatingLoader = $delegatingLoader;
		$this->localizedRouterFactory = $localizedRouterFactory;
		$this->domain = $domain;
	}

	/**
	 * @param int $domainId
	 * @return \Symfony\Component\Routing\Router
	 */
	public function getRouter($domainId) {
		if (!array_key_exists($domainId, $this->routersByDomainId)) {
			try {
				$domainConfig = $this->domain->getDomainConfigById($domainId);
			} catch (\SS6\ShopBundle\Model\Domain\Exception\InvalidDomainIdException $exception) {
				throw new \SS6\ShopBundle\Component\Router\Exception\RouterNotResolvedException('', $exception);
			}
			$context = $this->getRequestContextByDomainConfig($domainConfig);
			$basicRouter = $this->getBasicRouter($domainConfig);
			$localizedRouter = $this->localizedRouterFactory->getRouter($domainConfig->getLocale(), $context);
			$this->routersByDomainId[$domainId] = new DomainRouter($context, $basicRouter, $localizedRouter);
		}

		return $this->routersByDomainId[$domainId];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Component\Router\Router
	 */
	private function getBasicRouter(DomainConfig $domainConfig) {
		return new Router(
			$this->delegatingLoader,
			$this->routerConfiguration,
			[],
			$this->getRequestContextByDomainConfig($domainConfig)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return \Symfony\Component\Routing\RequestContext
	 */
	private function getRequestContextByDomainConfig(DomainConfig $domainConfig) {
		$urlComponents = parse_url($domainConfig->getUrl());
		$requestContext = new RequestContext($domainConfig->getUrl());
		$requestContext->setScheme($urlComponents[self::URL_SCHEME]);
		$requestContext->setHost($urlComponents[self::URL_HOST]);
		if ($urlComponents[self::URL_SCHEME] === self::URL_SCHEME_HTTP) {
			$requestContext->setHttpPort($urlComponents[self::URL_PORT]);
		} elseif ($urlComponents[self::URL_SCHEME] === self::URL_SCHEME_HTTPS) {
			$requestContext->setHttpsPort($urlComponents[self::URL_PORT]);
		}

		return $requestContext;
	}

}
