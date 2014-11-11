<?php

namespace SS6\ShopBundle\Component\Router;

use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class LocalizedRouterFactory {

	/**
	 * @var \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader
	 */
	private $delegatingLoader;

	/**
	 * @var array
	 */
	private $localeRoutersConfiguration;

	/**
	 * @var array
	 */
	private $routersByLocaleAndContextHost;

	
	public function __construct($localeRoutersConfiguration, DelegatingLoader $delegatingLoader) {
		$this->delegatingLoader = $delegatingLoader;
		$this->localeRoutersConfiguration = $localeRoutersConfiguration;
		$this->routersByLocaleAndContextHost = array();
	}

	/**
	 * @param string $locale
	 * @param \Symfony\Component\Routing\RequestContext $context
	 * @return \Symfony\Component\Routing\Router
	 * @throws \Exception
	 */
	public function getRouter($locale, RequestContext $context) {
		if (!array_key_exists($locale, $this->localeRoutersConfiguration)) {
			$message = 'Router with locale "' . $locale . '" does not have localized data.';
			throw new \SS6\ShopBundle\Component\Router\Exception\RouterNotResolvedException($message);
		}

		if (!array_key_exists($locale, $this->routersByLocaleAndContextHost) 
			|| !array_key_exists($context->getHost(), $this->routersByLocaleAndContextHost[$locale])
		) {
			$this->routersByLocaleAndContextHost[$locale][$context->getHost()] = new Router(
				$this->delegatingLoader,
				$this->localeRoutersConfiguration[$locale],
				array(),
				$context
			);
		}

		return $this->routersByLocaleAndContextHost[$locale][$context->getHost()];
	}

}
