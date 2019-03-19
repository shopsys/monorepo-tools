<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductAvailabilityFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductBrandFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCalculatedSellingDeniedFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCategoryFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCatnumFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductFlagFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductNameFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductPartnoFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductStockFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig as BaseProductAdvancedSearchConfig;

class ProductAdvancedSearchConfig extends BaseProductAdvancedSearchConfig
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCatnumFilter $productCatnumFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductNameFilter $productNameFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductPartnoFilter $productPartnoFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductStockFilter $productStockFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductFlagFilter $productFlagFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCalculatedSellingDeniedFilter $productCalculatedSellingDeniedFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductAvailabilityFilter $productAvailabilityFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductBrandFilter $productBrandFilter
     * @param \Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCategoryFilter $productCategoryFilter
     */
    public function __construct(
        ProductCatnumFilter $productCatnumFilter,
        ProductNameFilter $productNameFilter,
        ProductPartnoFilter $productPartnoFilter,
        ProductStockFilter $productStockFilter,
        ProductFlagFilter $productFlagFilter,
        ProductCalculatedSellingDeniedFilter $productCalculatedSellingDeniedFilter,
        ProductAvailabilityFilter $productAvailabilityFilter,
        ProductBrandFilter $productBrandFilter,
        ProductCategoryFilter $productCategoryFilter
    ) {
        parent::__construct(
            $productCatnumFilter,
            $productNameFilter,
            $productPartnoFilter,
            $productStockFilter,
            $productFlagFilter,
            $productCalculatedSellingDeniedFilter,
            $productAvailabilityFilter,
            $productBrandFilter
        );

        $this->registerFilter($productCategoryFilter);
    }
}
