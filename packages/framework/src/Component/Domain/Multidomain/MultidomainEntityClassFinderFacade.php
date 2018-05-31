<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Entity\EntityNotNullableColumnsFinder;
use Shopsys\FrameworkBundle\Component\Setting\SettingValue;
use Shopsys\FrameworkBundle\Model\Category\CategoryDomain;
use Shopsys\FrameworkBundle\Model\Mail\MailTemplate;
use Shopsys\FrameworkBundle\Model\Product\Brand\BrandDomain;
use Shopsys\FrameworkBundle\Model\Product\ProductDomain;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;

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
     * @var \Shopsys\FrameworkBundle\Component\Entity\EntityNotNullableColumnsFinder
     */
    protected $entityNotNullableColumnsFinder;

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
    protected function getIgnoredEntitiesNames()
    {
        return [
            SettingValue::class,
            ProductVisibility::class,
        ];
    }

    /**
     * @return string[]
     */
    protected function getManualMultidomainEntitiesNames()
    {
        return [
            BrandDomain::class,
            CategoryDomain::class,
            MailTemplate::class,
            ProductDomain::class,
        ];
    }
}
