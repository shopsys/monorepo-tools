<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use Symfony\Component\Routing\RouteCollection;

class FriendlyUrlMatcher
{

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
     */
    private $friendlyUrlRepository;

    public function __construct(FriendlyUrlRepository $friendlyUrlRepository) {
        $this->friendlyUrlRepository = $friendlyUrlRepository;
    }

    /**
     * @param string $pathinfo
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return array
     */
    public function match($pathinfo, RouteCollection $routeCollection, DomainConfig $domainConfig) {
        $friendlyUrl = $this->friendlyUrlRepository->findByDomainIdAndSlug($domainConfig->getId(), ltrim($pathinfo, '/'));

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

        if (!$friendlyUrl->isMain()) {
            $matchedParameters['_controller'] = 'FrameworkBundle:Redirect:redirect';
            $matchedParameters['route'] = $friendlyUrl->getRouteName();
            $matchedParameters['permanent'] = true;
        }

        return $matchedParameters;
    }

}
