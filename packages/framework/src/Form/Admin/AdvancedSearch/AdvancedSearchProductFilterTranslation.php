<?php

namespace Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductAvailabilityFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductBrandFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCalculatedSellingDeniedFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCategoryFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductCatnumFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductFlagFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductNameFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductPartnoFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\Filter\ProductStockFilter;

class AdvancedSearchProductFilterTranslation extends AdvancedSearchFilterTranslation
{
    public function __construct()
    {
        parent::__construct();

        $this->addFilterTranslation(ProductCatnumFilter::NAME, t('Catalogue number'));
        $this->addFilterTranslation(ProductFlagFilter::NAME, t('Flag'));
        $this->addFilterTranslation(ProductNameFilter::NAME, t('Product name'));
        $this->addFilterTranslation(ProductPartnoFilter::NAME, t('PartNo (serial number)'));
        $this->addFilterTranslation(ProductStockFilter::NAME, t('Stocks'));
        $this->addFilterTranslation(ProductCalculatedSellingDeniedFilter::NAME, t('Excluded from sale'));
        $this->addFilterTranslation(ProductAvailabilityFilter::NAME, t('Availability'));
        $this->addFilterTranslation(ProductBrandFilter::NAME, t('Brand'));
        $this->addFilterTranslation(ProductCategoryFilter::NAME, t('Category'));
    }
}
