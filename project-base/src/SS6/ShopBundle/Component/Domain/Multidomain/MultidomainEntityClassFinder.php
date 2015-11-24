<?php

namespace SS6\ShopBundle\Component\Domain\Multidomain;

class MultidomainEntityClassFinder {

	/**
	 * @param \Doctrine\ORM\Mapping\ClassMetadata[] $allClassesMetadata
	 * @return string[]
	 */
	public function getAllMultidomainEntitiesNames(array $allClassesMetadata) {
		$multidomainEntitiesNames = [];
		foreach ($allClassesMetadata as $classMetadata) {
			$identifierFieldNames = $classMetadata->getIdentifierFieldNames();
			if (count($identifierFieldNames) > 1 && in_array('domainId', $identifierFieldNames)) {
				$multidomainEntitiesNames[] = $classMetadata->getName();
			}
		}

		return $multidomainEntitiesNames;
	}
}
