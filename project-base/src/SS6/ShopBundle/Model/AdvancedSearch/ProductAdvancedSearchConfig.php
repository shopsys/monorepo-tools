<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductCalculatedSellingDeniedFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductCatnumFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductFlagFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductNameFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductPartnoFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductStockFilter;

class ProductAdvancedSearchConfig extends AdvancedSearchConfig {

	public function __construct(
		ProductCatnumFilter $productCatnumFilter,
		ProductNameFilter $productNameFilter,
		ProductPartnoFilter $productPartnoFilter,
		ProductStockFilter $productStockFilter,
		ProductFlagFilter $productFlagFilter,
		ProductCalculatedSellingDeniedFilter $productCalculatedSellingDeniedFilter
	) {
		parent::__construct();

		$this->registerFilter($productNameFilter);
		$this->registerFilter($productCatnumFilter);
		$this->registerFilter($productPartnoFilter);
		$this->registerFilter($productStockFilter);
		$this->registerFilter($productFlagFilter);
		$this->registerFilter($productCalculatedSellingDeniedFilter);
	}

}
