<?php

namespace SS6\ShopBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder;

class MultidomainEntityClassFinderFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder
	 */
	private $multidomainEntityClassFinder;

	public function __construct(EntityManager $em, MultidomainEntityClassFinder $multidomainEntityClassFinder) {
		$this->em = $em;
		$this->multidomainEntityClassFinder = $multidomainEntityClassFinder;
	}

	/**
	 * @return string[]
	 */
	public function getAllMultidomainEntitiesNames() {
		return $this->multidomainEntityClassFinder->getAllMultidomainEntitiesNames(
			$this->em->getMetadataFactory()->getAllMetadata()
		);
	}
}
