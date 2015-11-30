<?php

namespace SS6\ShopBundle\Component\Domain\Multidomain;

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
			$identifierFieldNames = $classMetadata->getIdentifierFieldNames();
			if (!$isEntityIgnored && count($identifierFieldNames) > 1 && in_array('domainId', $identifierFieldNames)) {
				$multidomainEntitiesNames[] = $classMetadata->getName();
			}
		}

		return $multidomainEntitiesNames;
	}

}
