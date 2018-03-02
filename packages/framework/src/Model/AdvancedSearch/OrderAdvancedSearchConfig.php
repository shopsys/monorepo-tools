<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
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

class OrderAdvancedSearchConfig extends AdvancedSearchConfig
{
    public function __construct(
        OrderNumberFilter $orderNumberFilter,
        OrderCreateDateFilter $orderCreateDateFilter,
        OrderPriceFilterWithVatFilter $orderPriceFilterWithVatFilter,
        OrderDomainFilter $orderDomainFilter,
        OrderStatusFilter $orderStatusFilter,
        OrderProductFilter $orderProductFilter,
        OrderPhoneNumberFilter $orderPhoneNumberFilter,
        OrderStreetFilter $orderStreetFilter,
        OrderNameFilter $orderNameFilter,
        OrderLastNameFilter $orderLastNameFilter,
        OrderEmailFilter $orderEmailFilter,
        OrderCityFilter $orderCityFilter,
        Domain $domain
    ) {
        parent::__construct();

        $this->registerFilter($orderPriceFilterWithVatFilter);
        $this->registerFilter($orderNumberFilter);
        $this->registerFilter($orderCreateDateFilter);
        $this->registerFilter($orderStatusFilter);
        if ($domain->isMultidomain()) {
            $this->registerFilter($orderDomainFilter);
        }
        $this->registerFilter($orderProductFilter);
        $this->registerFilter($orderNameFilter);
        $this->registerFilter($orderLastNameFilter);
        $this->registerFilter($orderEmailFilter);
        $this->registerFilter($orderPhoneNumberFilter);
        $this->registerFilter($orderStreetFilter);
        $this->registerFilter($orderCityFilter);
    }
}
