<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderCityFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderCreateDateFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderDomainFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderEmailFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderLastNameFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderNameFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderNumberFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderPhoneNumberFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderPriceFilterWithVatFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderProductFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderStatusFilter;
use Shopsys\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderStreetFilter;

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
        $this->registerFilter($orderPhoneNumberFilter);
        $this->registerFilter($orderStreetFilter);
        $this->registerFilter($orderNameFilter);
        $this->registerFilter($orderLastNameFilter);
        $this->registerFilter($orderEmailFilter);
        $this->registerFilter($orderCityFilter);
    }
}
