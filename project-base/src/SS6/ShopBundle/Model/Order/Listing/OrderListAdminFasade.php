<?php
/**
 * Author: Jakub Dolba
 * Date: 25. 4. 2015
 * Description:
 */

namespace SS6\ShopBundle\Model\Order\Listing;

class OrderListAdminFasade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository
	 */
	private $orderListAdminRepository;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository $productListAdminRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupSettingFacade
	 */
	public function __construct(
		OrderListAdminRepository $orderListAdminRepository
	) {
		$this->orderListAdminRepository = $orderListAdminRepository;
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getOrderListQueryBuilder() {
		return $this->orderListAdminRepository->getOrderListQueryBuilder();
	}

	/**
	 * @param array $searchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByExtendSearchData(array $searchData = null) {
		$queryBuilder = $this->getOrderListQueryBuilder();
		$this->orderListAdminRepository->extendQueryBuilderByExtendSearchData($queryBuilder, $searchData);

		return $queryBuilder;
	}

}