<?php

namespace Shopsys\FrameworkBundle\Form\Admin\AdvancedSearch;

use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderCityFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderCreateDateFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderDomainFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderEmailFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderLastNameFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderNameFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderNumberFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderPhoneNumberFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderPriceFilterWithVatFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderProductFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderStatusFilter;
use Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter\OrderStreetFilter;

class AdvancedSearchOrderFilterTranslation extends AdvancedSearchFilterTranslation
{
    public function __construct()
    {
        parent::__construct();

        $this->addFilterTranslation(OrderNumberFilter::NAME, t('Order number'));
        $this->addFilterTranslation(OrderCreateDateFilter::NAME, t('Created on'));
        $this->addFilterTranslation(OrderPriceFilterWithVatFilter::NAME, t('Price including VAT'));
        $this->addFilterTranslation(OrderDomainFilter::NAME, t('Domain'));
        $this->addFilterTranslation(OrderStatusFilter::NAME, t('Status of order'));
        $this->addFilterTranslation(OrderProductFilter::NAME, t('Product in order'));
        $this->addFilterTranslation(OrderPhoneNumberFilter::NAME, t('Customer phone number'));
        $this->addFilterTranslation(OrderStreetFilter::NAME, t('Customer street'));
        $this->addFilterTranslation(OrderNameFilter::NAME, t('Customer name'));
        $this->addFilterTranslation(OrderLastNameFilter::NAME, t('Customer last name'));
        $this->addFilterTranslation(OrderEmailFilter::NAME, t('Customer e-mail address'));
        $this->addFilterTranslation(OrderCityFilter::NAME, t('Customer city'));
    }
}
