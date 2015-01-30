<?php

namespace SS6\ShopBundle\Model\AdvanceSearch;

use SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface;
use SS6\ShopBundle\Model\AdvanceSearch\Filter\ProductCatnumFilter;
use SS6\ShopBundle\Model\AdvanceSearch\Filter\ProductNameFilter;

class AdvanceSearchConfig {

	/**
	 * @var \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface[]
	 */
	private $filters;

	public function __construct(
		ProductCatnumFilter $productCatnumFilter,
		ProductNameFilter $productNameFilter
	) {
		$this->filters = [];

		$this->registerFilter($productNameFilter);
		$this->registerFilter($productCatnumFilter);
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface $filter
	 */
	public function registerFilter(AdvanceSearchFilterInterface $filter) {
		if (array_key_exists($filter->getName(), $this->filters)) {
			$message = 'Filter "' . $filter->getName() . '" already exists.';
			throw new \SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchFilterAlreadyExistsException($message);
		}

		$this->filters[$filter->getName()] = $filter;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface[]
	 */
	public function getAllFilters() {
		return $this->filters;
	}

	/**
	 * @param string $filterName
	 * @return \SS6\ShopBundle\Model\AdvanceSearch\AdvanceSearchFilterInterface
	 */
	public function getFilter($filterName) {
		if (!array_key_exists($filterName, $this->filters)) {
			$message = 'Filter "' . $filterName . '" not found.';
			throw new \SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchFilterNotFoundException($message);
		}

		return $this->filters[$filterName];
	}
}
