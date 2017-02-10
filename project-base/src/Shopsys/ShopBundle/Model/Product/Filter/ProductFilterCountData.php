<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

class ProductFilterCountData {

	/**
	 * @var int
	 */
	public $countInStock;

	/*
	 * @var int[brandId]
	 */
	public $countByBrandId;

	/**
	 * @var int[flagId]
	 */
	public $countByFlagId;

	/**
	 * @var int[parameterId][parameterValueId]
	 */
	public $countByParameterIdAndValueId;

}
