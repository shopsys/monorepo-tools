<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository;

class ProductListAdminFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository
	 */
	private $productListAdminRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository $productListAdminRepository
	 */
	public function __construct(EntityManager $em, ProductListAdminRepository $productListAdminRepository) {
		$this->em = $em;
		$this->productListAdminRepository = $productListAdminRepository;
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
		$this->productListAdminRepository->extendQueryBuilderByQuickSearchData($queryBuilder, $searchData);

		return $queryBuilder;
	}
}
