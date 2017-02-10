<?php

namespace Shopsys\ShopBundle\Form\Admin\AdvancedSearch;

class AdvancedSearchProductFilterTranslation extends AdvancedSearchFilterTranslation
{
    public function __construct() {
        parent::__construct();

        $this->addFilterTranslation('productCatnum', t('Catalogue number'));
        $this->addFilterTranslation('productFlag', t('Flag'));
        $this->addFilterTranslation('productName', t('Product name'));
        $this->addFilterTranslation('productPartno', t('PartNo (serial number)'));
        $this->addFilterTranslation('productStock', t('Stocks'));
        $this->addFilterTranslation('productCalculatedSellingDenied', t('Excluded from sale'));
        $this->addFilterTranslation('productAvailability', t('Availability'));
        $this->addFilterTranslation('productBrand', t('Brand'));
    }
}
