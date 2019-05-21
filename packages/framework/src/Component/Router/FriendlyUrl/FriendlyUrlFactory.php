<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\String\TransformString;

class FriendlyUrlFactory implements FriendlyUrlFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(
        Domain $domain,
        EntityNameResolver $entityNameResolver
    ) {
        $this->domain = $domain;
        $this->entityNameResolver = $entityNameResolver;
    }

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
        $classData = $this->entityNameResolver->resolve(FriendlyUrl::class);

        return new $classData($routeName, $entityId, $domainId, $slug);
    }

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
    ): ?FriendlyUrl {
        if ($entityName === '') {
            return null;
        }

        $nameForUrl = $entityName . ($indexPostfix === null ? '' : '-' . $indexPostfix);
        $slug = TransformString::stringToFriendlyUrlSlug($nameForUrl) . '/';

        return $this->create($routeName, $entityId, $domainId, $slug);
    }

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
    ): array {
        $friendlyUrls = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            if (array_key_exists($domainConfig->getLocale(), $namesByLocale)) {
                $friendlyUrl = $this->createIfValid(
                    $routeName,
                    $entityId,
                    (string)$namesByLocale[$domainConfig->getLocale()],
                    $domainConfig->getId()
                );

                if ($friendlyUrl !== null) {
                    $friendlyUrls[] = $friendlyUrl;
                }
            }
        }

        return $friendlyUrls;
    }
}
