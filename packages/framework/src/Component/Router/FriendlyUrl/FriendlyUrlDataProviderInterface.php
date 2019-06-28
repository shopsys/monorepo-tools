<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

interface FriendlyUrlDataProviderInterface
{
    /**
     * Returns friendly url data for generating urls
     *
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlData[]
     */
    public function getFriendlyUrlData(DomainConfig $domainConfig): array;

    /**
     * Returns route name that specifies for which route should be data provider used
     *
     * @return string
     */
    public function getRouteName(): string;
}
