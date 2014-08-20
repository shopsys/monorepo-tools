<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;

class ProductListAdminFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @param array|null $searchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByQuickSearchData(array $searchData = null) {
		$queryBuilder = $this->em->createQueryBuilder();
		$queryBuilder
			->select('p')
			->from(Product::class, 'p');

		if ($searchData['text'] !== null && $searchData['text'] !== '') {
			$queryBuilder->andWhere('
				(
					p.name LIKE :text OR
					p.catnum LIKE :text OR
					p.partno LIKE :text
				)');
			$querySerachText = '%' .$searchData['text'] . '%';
			$queryBuilder->setParameter('text', $querySerachText);
		}

		return $queryBuilder;
	}
}
