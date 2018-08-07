<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

class FriendlyUrlFactory implements FriendlyUrlFactoryInterface
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
    ): FriendlyUrl {
        return new FriendlyUrl($routeName, $entityId, $domainId, $slug);
    }
}
