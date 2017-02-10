<?php

namespace Shopsys\ShopBundle\Component\Domain\Multidomain;

use Doctrine\ORM\Mapping\ClassMetadata;

class MultidomainEntityClassFinder {

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata[] $allClassesMetadata
     * @param string[] $ignoredEntitiesNames
     * @param string[] $manualMultidomainEntitiesNames
     * @return string[]
     */
    public function getMultidomainEntitiesNames(
        array $allClassesMetadata,
        array $ignoredEntitiesNames,
        array $manualMultidomainEntitiesNames
    ) {
        $multidomainEntitiesNames = [];
        foreach ($allClassesMetadata as $classMetadata) {
            $entityName = $classMetadata->getName();
            $isEntityIgnored = in_array($entityName, $ignoredEntitiesNames, true);
            $isManualMultidomainEntity = in_array($entityName, $manualMultidomainEntitiesNames, true);
            if (
                $isManualMultidomainEntity
                || !$isEntityIgnored && $this->isMultidomainEntity($classMetadata)
            ) {
                $multidomainEntitiesNames[] = $classMetadata->getName();
            }
        }

        return $multidomainEntitiesNames;
    }

    /**
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @return bool
     */
    private function isMultidomainEntity(ClassMetadata $classMetadata) {
        $identifierFieldNames = $classMetadata->getIdentifierFieldNames();

        return count($identifierFieldNames) > 1 && in_array('domainId', $identifierFieldNames);
    }

}
