<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class MultidomainEntityClassFinderFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder
     */
    protected $multidomainEntityClassFinder;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassProviderInterface
     */
    protected $multidomainEntityClassProvider;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder
     */
    protected $notNullableColumnsFinder;

    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder $multidomainEntityClassFinder
     * @param \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassProviderInterface $multidomainEntityClassProvider
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder $notNullableColumnsFinder
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(
        EntityManagerInterface $em,
        MultidomainEntityClassFinder $multidomainEntityClassFinder,
        MultidomainEntityClassProviderInterface $multidomainEntityClassProvider,
        NotNullableColumnsFinder $notNullableColumnsFinder,
        EntityNameResolver $entityNameResolver
    ) {
        $this->em = $em;
        $this->multidomainEntityClassFinder = $multidomainEntityClassFinder;
        $this->multidomainEntityClassProvider = $multidomainEntityClassProvider;
        $this->notNullableColumnsFinder = $notNullableColumnsFinder;
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @return string[]
     */
    public function getMultidomainEntitiesNames()
    {
        return $this->multidomainEntityClassFinder->getMultidomainEntitiesNames(
            $this->em->getMetadataFactory()->getAllMetadata(),
            $this->multidomainEntityClassProvider->getIgnoredMultidomainEntitiesNames(),
            $this->multidomainEntityClassProvider->getManualMultidomainEntitiesNames()
        );
    }

    /**
     * @return string[][]
     */
    public function getAllNotNullableColumnNamesIndexedByTableName()
    {
        $multidomainClassesMetadata = [];
        foreach ($this->getMultidomainEntitiesNames() as $multidomainEntityName) {
            $resolvedClassName = $this->entityNameResolver->resolve($multidomainEntityName);
            $multidomainClassesMetadata[] = $this->em->getMetadataFactory()->getMetadataFor($resolvedClassName);
        }

        return $this->notNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName($multidomainClassesMetadata);
    }
}
