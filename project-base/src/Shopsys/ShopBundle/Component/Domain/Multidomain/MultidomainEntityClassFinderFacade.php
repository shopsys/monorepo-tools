<?php

namespace Shopsys\ShopBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder;
use Shopsys\ShopBundle\Component\Entity\EntityNotNullableColumnsFinder;
use Shopsys\ShopBundle\Component\Setting\SettingValue;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Product\ProductVisibility;

class MultidomainEntityClassFinderFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder
     */
    private $multidomainEntityClassFinder;

    /**
     * @var \Shopsys\ShopBundle\Component\Entity\EntityNotNullableColumnsFinder
     */
    private $entityNotNullableColumnsFinder;

    public function __construct(
        EntityManager $em,
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
    public function getMultidomainEntitiesNames() {
        return $this->multidomainEntityClassFinder->getMultidomainEntitiesNames(
            $this->em->getMetadataFactory()->getAllMetadata(),
            $this->getIgnoredEntitiesNames(),
            $this->getManualMultidomainEntitiesNames()
        );
    }

    /**
     * @return string[tableName][]
     */
    public function getAllNotNullableColumnNamesIndexedByTableName() {
        $multidomainClassesMetadata = [];
        foreach ($this->getMultidomainEntitiesNames() as $multidomainEntityName) {
            $multidomainClassesMetadata[] = $this->em->getMetadataFactory()->getMetadataFor($multidomainEntityName);
        }

        return $this->entityNotNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName($multidomainClassesMetadata);
    }

    /**
     * @return string[]
     */
    private function getIgnoredEntitiesNames() {
        return [
            SettingValue::class,
            ProductVisibility::class,
        ];
    }

    /**
     * @return string[]
     */
    private function getManualMultidomainEntitiesNames() {
        return [
            MailTemplate::class,
        ];
    }
}
