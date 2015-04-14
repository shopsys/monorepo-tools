<?php

namespace SS6\ShopBundle\Component\Router\FriendlyUrl;

use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use Symfony\Component\Routing\RouteCollection;

class FriendlyUrlMatcher {

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
	 */
	private $friendlyUrlRepository;

	public function __construct(FriendlyUrlRepository $friendlyUrlRepository) {
		$this->friendlyUrlRepository = $friendlyUrlRepository;
	}

	/**
	 * @param string $pathinfo
	 * @param \Symfony\Component\Routing\RouteCollection $routeCollection
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return array
	 */
	public function match($pathinfo, RouteCollection $routeCollection, DomainConfig $domainConfig) {
		$friendlyUrl = $this->friendlyUrlRepository->findByDomainIdAndSlug($domainConfig->getId(), trim($pathinfo, '/'));

		if ($friendlyUrl === null) {
			throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
		}

		$route = $routeCollection->get($friendlyUrl->getRouteName());
		if ($route === null) {
			throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
		}

		$matchedParameters = $route->getDefaults();
		$matchedParameters['_route'] = $friendlyUrl->getRouteName();
		$matchedParameters['id'] = $friendlyUrl->getEntityId();

		return $matchedParameters;
	}

}
