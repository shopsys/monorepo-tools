<?php

namespace SS6\ShopBundle\Component\Router;

use SS6\ShopBundle\Component\Router\LocalizedRouterFactory;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class DomainRouterFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Router\LocalizedRouterFactory
	 */
	private $localizedRouterFactory;

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	private $requestStack;

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
	private $routersByDomainId = array();

	public function __construct(
		$routerConfiguration,
		RequestStack $requestStack,
		DelegatingLoader $delegatingLoader,
		LocalizedRouterFactory $localizedRouterFactory,
		Domain $domain
	) {
		$this->routerConfiguration = $routerConfiguration;
		$this->requestStack = $requestStack;
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
				throw new Exception\RouterNotResolvedException('', $exception);
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
			array(),
			$this->getRequestContextByDomainConfig($domainConfig)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return \Symfony\Component\Routing\RequestContext
	 */
	private function getRequestContextByDomainConfig(DomainConfig $domainConfig) {
		$requestContext = new RequestContext();
		$masterRequest = $this->requestStack->getMasterRequest();
		if ($masterRequest === null) {
			$masterRequest = new Request();
		}
		$masterHost = $masterRequest->getHost();
		$domainHost = $domainConfig->getDomain();

		$requestContext->fromRequest($masterRequest);
		$requestContext->setHost($domainHost);
		$requestContext->setBaseUrl(str_replace($masterHost, $domainHost, $masterRequest->getBaseUrl()));
		$requestContext->setPathInfo(str_replace($masterHost, $domainHost, $masterRequest->getPathInfo()));

		return $requestContext;
	}

}
