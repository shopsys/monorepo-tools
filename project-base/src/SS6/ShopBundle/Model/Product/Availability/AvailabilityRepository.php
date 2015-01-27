<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use SS6\ShopBundle\Component\DoctrineWalker\SortableNullsWalker;
use SS6\ShopBundle\Model\Product\Availability\Availability;

class AvailabilityRepository {

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getAvailabilityRepository() {
		return $this->em->getRepository(Availability::class);
	}

	/**
	 * @param int $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function findById($availabilityId) {
		return $this->getAvailabilityRepository()->find($availabilityId);
	}

	/**
	 * @param int $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function getById($availabilityId) {
		$availability = $this->findById($availabilityId);

		if ($availability === null) {
			throw new \SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException($availabilityId);
		}

		return $availability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	public function getAll() {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('a')
			->from(Availability::class, 'a')
			->orderBy('a.deliveryTime');
		$query = $queryBuilder->getQuery();
		$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		return $query->execute();
	}

}
