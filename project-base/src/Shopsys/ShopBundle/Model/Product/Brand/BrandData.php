<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\ShopBundle\Form\UrlListData;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;

class BrandData {

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string[]
	 */
	public $image;

	/**
	 * @var string[]
	 */
	public $descriptions;

	/**
	 * @var \Shopsys\ShopBundle\Form\UrlListData
	 */
	public $urls;

	public function __construct() {
		$this->name = '';
		$this->image = [];
		$this->descriptions = [];
		$this->urls = new UrlListData();
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand $brand
	 */
	public function setFromEntity(Brand $brand) {
		$this->name = $brand->getName();

		$translations = $brand->getTranslations();
		/* @var $translations \Shopsys\ShopBundle\Model\Product\Brand\BrandTranslation[]  */

		$this->descriptions = [];
		foreach ($translations as $translate) {
			$this->descriptions[$translate->getLocale()] = $translate->getDescription();
		}
	}

}
