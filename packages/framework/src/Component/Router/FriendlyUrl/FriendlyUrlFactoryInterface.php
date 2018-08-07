<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

interface FriendlyUrlFactoryInterface
{
    /**
     * @param string $routeName
     * @param int $entityId
     * @param int $domainId
     * @param string $slug
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl
     */
    public function create(
        string $routeName,
        int $entityId,
        int $domainId,
        string $slug
    ): FriendlyUrl;
}
