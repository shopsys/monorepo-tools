<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository;

class ProductListAdminFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository
	 */
	private $productListAdminRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository $productListAdminRepository
	 */
	public function __construct(ProductListAdminRepository $productListAdminRepository) {
		$this->productListAdminRepository = $productListAdminRepository;
	}

	/**
	 * @param array|null $searchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByQuickSearchData(array $searchData = null) {
		return $this->productListAdminRepository->getQueryBuilderByQuickSearchData($searchData);
	}

}
