<?php

namespace Shopsys\ShopBundle\Model\Order\Listing;

class OrderListAdminFacade {

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminRepository
	 */
	private $orderListAdminRepository;

	/**
	 * @param \Shopsys\ShopBundle\Model\Order\Listing\OrderListAdminRepository $productListAdminRepository
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupSettingFacade
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
