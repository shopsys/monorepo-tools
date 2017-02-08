<?php

namespace SS6\ShopBundle\Model\AdvancedSearch;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderCreateDateFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderDomainFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderNumberFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderPriceFilterWithVatFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderProductFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderStatusFilter;

class OrderAdvancedSearchConfig extends AdvancedSearchConfig {

	public function __construct(
		OrderNumberFilter $orderNumberFilter,
		OrderCreateDateFilter $orderCreateDateFilter,
		OrderPriceFilterWithVatFilter $orderPriceFilterWithVatFilter,
		OrderDomainFilter $orderDomainFilter,
		OrderStatusFilter $orderStatusFilter,
		OrderProductFilter $orderProductFilter,
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
	}

}
