<?php

namespace Shopsys\ShopBundle\Form\Admin\AdvancedSearch;

class AdvancedSearchOrderFilterTranslation extends AdvancedSearchFilterTranslation
{
    public function __construct() {
        parent::__construct();

        $this->addFilterTranslation('orderNumber', t('Order number'));
        $this->addFilterTranslation('orderCreatedAt', t('Created on'));
        $this->addFilterTranslation('orderTotalPriceWithVat', t('Price including VAT'));
        $this->addFilterTranslation('orderDomain', t('Domain'));
        $this->addFilterTranslation('orderStatus', t('Status of order'));
        $this->addFilterTranslation('orderProduct', t('Product in order'));
    }
}
