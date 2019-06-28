<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Webmozart\Assert\Assert;

class FriendlyUrlDataProviderRegistry
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlDataProviderInterface[]
     */
    protected $friendlyUrlDataProviders;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlDataProviderInterface[] $friendlyUrlDataProviders
     */
    public function __construct(iterable $friendlyUrlDataProviders)
    {
        Assert::allIsInstanceOf($friendlyUrlDataProviders, FriendlyUrlDataProviderInterface::class);

        $this->friendlyUrlDataProviders = $friendlyUrlDataProviders;
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
