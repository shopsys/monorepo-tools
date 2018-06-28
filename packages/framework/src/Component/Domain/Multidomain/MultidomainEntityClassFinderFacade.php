<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\NotNullableColumnsFinder;

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

    public function __construct(
        EntityManagerInterface $em,
        MultidomainEntityClassFinder $multidomainEntityClassFinder,
        MultidomainEntityClassProviderInterface $multidomainEntityClassProvider,
        NotNullableColumnsFinder $notNullableColumnsFinder
    ) {
        $this->em = $em;
        $this->multidomainEntityClassFinder = $multidomainEntityClassFinder;
        $this->multidomainEntityClassProvider = $multidomainEntityClassProvider;
        $this->notNullableColumnsFinder = $notNullableColumnsFinder;
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
            $multidomainClassesMetadata[] = $this->em->getMetadataFactory()->getMetadataFor($multidomainEntityName);
        }

        return $this->notNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName($multidomainClassesMetadata);
    }
}
