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

    /**
     * @param string $routeName
     * @param int $entityId
     * @param string $entityName
     * @param int $domainId
     * @param int|null $indexPostfix
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl|null
     */
    public function createIfValid(
        string $routeName,
        int $entityId,
        string $entityName,
        int $domainId,
        ?int $indexPostfix = null
    ): ?FriendlyUrl;

    /**
     * @param string $routeName
     * @param int $entityId
     * @param string[] $namesByLocale
     * @return \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrl[]
     */
    public function createForAllDomains(
        string $routeName,
        int $entityId,
        array $namesByLocale
    ): array;
}
