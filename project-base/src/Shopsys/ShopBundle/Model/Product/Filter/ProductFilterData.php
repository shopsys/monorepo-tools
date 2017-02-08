<?php

namespace SS6\ShopBundle\Model\Product\Filter;

class ProductFilterData {

	/**
	 * @var string
	 */
	public $minimalPrice;

	/**
	 * @var string
	 */
	public $maximalPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ParameterFilterData[]
	 */
	public $parameters = [];

	/**
	 * @var bool
	 */
	public $inStock;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public $flags = [];

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand[]
	 */
	public $brands = [];

}
