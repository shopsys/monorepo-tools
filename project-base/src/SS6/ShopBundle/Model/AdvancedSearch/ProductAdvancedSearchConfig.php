<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductCatnumFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductNameFilter;
use SS6\ShopBundle\Model\AdvancedSearch\Filter\ProductPartnoFilter;

class ProductAdvancedSearchConfig extends AdvancedSearchConfig {

	public function __construct(
		ProductCatnumFilter $productCatnumFilter,
		ProductNameFilter $productNameFilter,
		ProductPartnoFilter $productPartnoFilter
	) {
		parent::__construct();

		$this->registerFilter($productNameFilter);
		$this->registerFilter($productCatnumFilter);
		$this->registerFilter($productPartnoFilter);
	}

}
