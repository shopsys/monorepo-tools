<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductAvailabilityFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductBrandFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductCalculatedSellingDeniedFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductCatnumFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductFlagFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductNameFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductPartnoFilter;
use Shopsys\ShopBundle\Model\AdvancedSearch\Filter\ProductStockFilter;

class ProductAdvancedSearchConfig extends AdvancedSearchConfig
{

    public function __construct(
        ProductCatnumFilter $productCatnumFilter,
        ProductNameFilter $productNameFilter,
        ProductPartnoFilter $productPartnoFilter,
        ProductStockFilter $productStockFilter,
        ProductFlagFilter $productFlagFilter,
        ProductCalculatedSellingDeniedFilter $productCalculatedSellingDeniedFilter,
        ProductAvailabilityFilter $productAvailabilityFilter,
        ProductBrandFilter $productBrandFilter
    ) {
        parent::__construct();

        $this->registerFilter($productNameFilter);
        $this->registerFilter($productCatnumFilter);
        $this->registerFilter($productPartnoFilter);
        $this->registerFilter($productStockFilter);
        $this->registerFilter($productFlagFilter);
        $this->registerFilter($productCalculatedSellingDeniedFilter);
        $this->registerFilter($productAvailabilityFilter);
        $this->registerFilter($productBrandFilter);
    }

}
