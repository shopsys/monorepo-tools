<?php

namespace SS6\ShopBundle\Model\AdvancedSearchOrder;

use SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderCreateDateFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderDomainFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderNumberFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderPriceFilterWithVatFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderProductFilter;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Filter\OrderStatusFilter;

class AdvancedSearchOrderConfig {

	/**
	 * @var \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface[]
	 */
	private $filters;

	public function __construct(
		OrderNumberFilter $orderNumberFilter,
		OrderCreateDateFilter $orderCreateDateFilter,
		OrderPriceFilterWithVatFilter $orderPriceFilterWithVatFilter,
		OrderDomainFilter $orderDomainFilter,
		OrderStatusFilter $orderStatusFilter,
		OrderProductFilter $orderProductFilter
	) {
		$this->filters = [];

		$this->registerFilter($orderPriceFilterWithVatFilter);
		$this->registerFilter($orderNumberFilter);
		$this->registerFilter($orderCreateDateFilter);
		$this->registerFilter($orderStatusFilter);
		$this->registerFilter($orderDomainFilter);
		$this->registerFilter($orderProductFilter);
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface $filter
	 */
	public function registerFilter(AdvancedSearchOrderFilterInterface $filter) {
		if (array_key_exists($filter->getName(), $this->filters)) {
			$message = 'Filter "' . $filter->getName() . '" already exists.';
			throw new \SS6\ShopBundle\Model\AdvancedSearchOrder\Exception\AdvancedSearchOrderFilterAlreadyExistsException($message);
		}

		$this->filters[$filter->getName()] = $filter;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface[]
	 */
	public function getAllFilters() {
		return $this->filters;
	}

	/**
	 * @param string $filterName
	 * @return \SS6\ShopBundle\Model\AdvancedSearchOrder\AdvancedSearchOrderFilterInterface
	 */
	public function getFilter($filterName) {
		if (!array_key_exists($filterName, $this->filters)) {
			$message = 'Filter "' . $filterName . '" not found.';
			throw new \SS6\ShopBundle\Model\AdvancedSearchOrder\Exception\AdvancedSearchOrderFilterNotFoundException($message);
		}

		return $this->filters[$filterName];
	}
}
