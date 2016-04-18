<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use SS6\ShopBundle\Model\Product\Brand\Brand;

class BrandData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @param string $name
	 */
	public function __construct($name = null) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand $brand
	 */
	public function setFromEntity(Brand $brand) {
		$this->name = $brand->getName();
	}

}
