<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Entity\EntityNotNullableColumnsFinder;
use Shopsys\FrameworkBundle\Component\Setting\SettingValue;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;

class MultidomainEntityClassFinderFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder
     */
    private $multidomainEntityClassFinder;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Entity\EntityNotNullableColumnsFinder
     */
    private $entityNotNullableColumnsFinder;

    public function __construct(
        EntityManagerInterface $em,
        MultidomainEntityClassFinder $multidomainEntityClassFinder,
        EntityNotNullableColumnsFinder $entityNotNullableColumnsFinder
    ) {
        $this->em = $em;
        $this->multidomainEntityClassFinder = $multidomainEntityClassFinder;
        $this->entityNotNullableColumnsFinder = $entityNotNullableColumnsFinder;
    }

    /**
     * @return string[]
     */
    public function getMultidomainEntitiesNames()
    {
        return $this->multidomainEntityClassFinder->getMultidomainEntitiesNames(
            $this->em->getMetadataFactory()->getAllMetadata(),
            $this->getIgnoredEntitiesNames(),
            $this->getManualMultidomainEntitiesNames()
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

        return $this->entityNotNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName($multidomainClassesMetadata);
    }

    /**
     * @return string[]
     */
    private function getIgnoredEntitiesNames()
    {
        return [
            SettingValue::class,
            ProductVisibility::class,
        ];
    }

    /**
     * @return string[]
     */
    private function getManualMultidomainEntitiesNames()
    {
        return [
            MailTemplate::class,
        ];
    }
}
