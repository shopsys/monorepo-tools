<?php

namespace SS6\ShopBundle\Model\Product\Brand;

use SS6\ShopBundle\Model\Product\Brand\Brand;

class BrandData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string[]
	 */
	public $image;

	public function __construct() {
		$this->name = '';
		$this->image = [];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand $brand
	 */
	public function setFromEntity(Brand $brand) {
		$this->name = $brand->getName();
	}

}
