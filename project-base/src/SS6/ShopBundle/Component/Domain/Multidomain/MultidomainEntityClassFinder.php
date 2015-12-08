<?php

namespace SS6\ShopBundle\Component\Domain\Multidomain;

use Doctrine\ORM\Mapping\ClassMetadata;

class MultidomainEntityClassFinder {

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata[] $allClassesMetadata
	 * @param string[] $ignoredEntitiesNames
	 * @return string[]
	 */
	public function getMultidomainEntitiesNames(array $allClassesMetadata, array $ignoredEntitiesNames) {
		$multidomainEntitiesNames = [];
		foreach ($allClassesMetadata as $classMetadata) {
			$entityName = $classMetadata->getName();
			$isEntityIgnored = in_array($entityName, $ignoredEntitiesNames, true);
			if (!$isEntityIgnored && $this->isMultidomainEntity($classMetadata)) {
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
