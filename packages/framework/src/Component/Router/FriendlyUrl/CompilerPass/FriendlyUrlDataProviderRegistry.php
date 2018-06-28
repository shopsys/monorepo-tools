<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class FriendlyUrlDataProviderRegistry
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass\FriendlyUrlDataProviderInterface[]
     */
    private $friendlyUrlDataProviders;

    public function __construct()
    {
        $this->friendlyUrlDataProviders = [];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\CompilerPass\FriendlyUrlDataProviderInterface $service
     */
    public function registerFriendlyUrlDataProvider(FriendlyUrlDataProviderInterface $service)
    {
        $this->friendlyUrlDataProviders[] = $service;
    }

    /**
     * @param string $routeName
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    public function getFriendlyUrlDataByRouteAndDomain($routeName, DomainConfig $domainConfig)
    {
        foreach ($this->friendlyUrlDataProviders as $friendlyUrlDataProvider) {
            if ($friendlyUrlDataProvider->getRouteName() === $routeName) {
                return $friendlyUrlDataProvider->getFriendlyUrlData($domainConfig);
            }
        }

        throw new \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlRouteNotSupportedException($routeName);
    }
}
