<?php

namespace SS6\ShopBundle\Model\Feed\Category;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Feed\Category\FeedCategory;

class FeedCategoryRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	private function getFeedCategoryRepository() {
		return $this->em->getRepository(FeedCategory::class);
	}

	/**
	 * @param int $extId
	 */
	public function findByExtId($extId) {
		return $this->getFeedCategoryRepository()->findOneBy([
			'extId' => $extId,
		]);
	}

	/**
	 * @param int[] $extIds
	 */
	public function deleteByExtIdNotIn(array $extIds) {
		$qb = $this->em->createQueryBuilder();

		$qb->delete(FeedCategory::class, 'fc')
			->where('fc.extId NOT IN (:currentExtIds)')
			->setParameter('currentExtIds', $extIds);

		$qb->getQuery()->execute();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\Category\FeedCategory[extId]
	 */
	public function getAllIndexedByExtId() {
		return $this->getFeedCategoryRepository()->createQueryBuilder('fc', 'fc.extId')->getQuery()->execute();
	}

}
