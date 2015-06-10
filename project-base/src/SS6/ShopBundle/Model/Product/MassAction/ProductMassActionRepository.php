<?php

namespace SS6\ShopBundle\Model\Product\MassAction;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;

class ProductMassActionRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(
		EntityManager $entityManager
	) {
		$this->em = $entityManager;
	}

	/**
	 * @param int[] $selectedProductIds
	 * @param boolean $hidden
	 */
	public function setHidden(array $selectedProductIds, $hidden) {
		$updateQueryBuilder = $this->em->createQueryBuilder()
			->update(Product::class, 'p')
			->set('p.hidden', ':value')->setParameter('value', $hidden)
			->where('p.id IN (:productIds)')->setParameter('productIds', $selectedProductIds);

		$updateQueryBuilder->getQuery()->execute();
	}

}
