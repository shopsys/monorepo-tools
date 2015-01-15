<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\ProductData;

class ProductEditData {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductData
	 */
	public $productData;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	public $parameters;

	/**
	 * @var string[]
	 */
	public $imagesToUpload;

	/**
	 * @var \SS6\ShopBundle\Model\Image\Image[]
	 */
	public $imagesToDelete;

	/**
	 * @var string[]
	 */
	public $manualInputPrices;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $parameters
	 * @param string[] $imagesToUpload
	 * @param \SS6\ShopBundle\Model\Image\Image[] $imagesToDelete
	 * @param string[] $manualInputPrices
	 */
	public function __construct(
		ProductData $productData = null,
		array $parameters = [],
		array $imagesToUpload = [],
		array $imagesToDelete = [],
		array $manualInputPrices = []
	) {
		if ($productData !== null) {
			$this->productData = $productData;
		} else {
			$this->productData = new ProductData();
		}
		$this->parameters = $parameters;
		$this->imagesToUpload = $imagesToUpload;
		$this->imagesToDelete = $imagesToDelete;
		$this->manualInputPrices = $manualInputPrices;
	}


}
