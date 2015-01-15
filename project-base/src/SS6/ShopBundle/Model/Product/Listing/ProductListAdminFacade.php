<?php

namespace SS6\ShopBundle\Model\Product\Listing;

use \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository;

class ProductListAdminFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository
	 */
	private $productListAdminRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Listing\ProductListAdminRepository $productListAdminRepository
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade
	 */
	public function __construct(ProductListAdminRepository $productListAdminRepository, PricingGroupFacade $pricingGroupFacade) {
		$this->productListAdminRepository = $productListAdminRepository;
		$this->pricingGroupFacade = $pricingGroupFacade;
	}

	/**
	 * @param array|null $searchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilderByQuickSearchData(array $searchData = null) {
		/*
		 * temporary solution -
		 * when product price type calculation is set to manual, price for first domain is shown in admin product list
		 */
		$defaultPricingGroupId = $this->pricingGroupFacade->getDefaultPricingGroupByDomainId(1)->getId();
		return $this->productListAdminRepository->getQueryBuilderByQuickSearchData($defaultPricingGroupId, $searchData);
	}

}
