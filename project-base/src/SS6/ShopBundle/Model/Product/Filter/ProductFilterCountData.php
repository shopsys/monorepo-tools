<?php

namespace SS6\ShopBundle\Model\Product\Filter;

class ProductFilterCountData {

	/**
	 * @var int
	 */
	public $countInStock;

	/**
	 * @var int[flagId]
	 */
	public $countByFlagId;

	/**
	 * @var int[parameterId][parameterValueId]
	 */
	public $countByParameterIdAndValueId;

}
