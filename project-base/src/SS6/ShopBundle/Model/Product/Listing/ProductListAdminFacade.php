<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository;

class ProductListAdminFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository
	 */
	private $productListAdminRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository $productListAdminRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupSettingFacade
	 */
	public function __construct(
		ProductListAdminRepository $productListAdminRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade
	) {
		$this->productListAdminRepository = $productListAdminRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getProductListQueryBuilder() {
		/**
		 * temporary solution -
		 * when product price type calculation is set to manual, price for first domain is shown in admin product list
		 */
		$defaultPricingGroupId = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId(1)->getId();

		return $this->productListAdminRepository->getProductListQueryBuilder($defaultPricingGroupId);
	}

	/**
	 * @param array|null $searchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByQuickSearchData(array $searchData = null) {
		$queryBuilder = $this->getProductListQueryBuilder();
		$this->productListAdminRepository->extendQueryBuilderByQuickSearchData($queryBuilder, $searchData);

		return $queryBuilder;
	}

}
