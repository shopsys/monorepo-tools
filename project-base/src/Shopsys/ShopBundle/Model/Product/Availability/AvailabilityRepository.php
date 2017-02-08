<?php

namespace SS6\ShopBundle\Model\Product\Availability;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use SS6\ShopBundle\Component\Doctrine\SortableNullsWalker;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Product;

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
			$message = 'Availability with ID ' . $availabilityId . ' not found.';
			throw new \SS6\ShopBundle\Model\Product\Availability\Exception\AvailabilityNotFoundException($message);
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
			->orderBy('a.dispatchTime');
		$query = $queryBuilder->getQuery();
		$query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, SortableNullsWalker::class);

		return $query->execute();
	}

	/**
	 * @param int $availabilityId
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability[]
	 */
	public function getAllExceptId($availabilityId) {
		$qb = $this->getAvailabilityRepository()->createQueryBuilder('a')
			->where('a.id != :id')
			->setParameter('id', $availabilityId);

		return $qb->getQuery()->getResult();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 * @return bool
	 */
	public function isAvailabilityUsed(Availability $availability) {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('p.id')
			->from(Product::class, 'p')
			->setMaxResults(1)
			->where('p.availability = :availability OR p.outOfStockAvailability = :availability')
			->setParameter('availability', $availability->getId());

		return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_SCALAR) !== null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $oldAvailability
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $newAvailability
	 */
	public function replaceAvailability(Availability $oldAvailability, Availability $newAvailability) {
		$this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.availability', ':newAvailability')->setParameter('newAvailability', $newAvailability)
			->where('p.availability = :oldAvailability')->setParameter('oldAvailability', $oldAvailability)
			->getQuery()->execute();

		$this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.outOfStockAvailability', ':newAvailability')->setParameter('newAvailability', $newAvailability)
			->where('p.outOfStockAvailability = :oldAvailability')->setParameter('oldAvailability', $oldAvailability)
			->getQuery()->execute();

		$this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.calculatedAvailability', ':newAvailability')->setParameter('newAvailability', $newAvailability)
			->where('p.calculatedAvailability = :oldAvailability')->setParameter('oldAvailability', $oldAvailability)
			->getQuery()->execute();
	}

}
