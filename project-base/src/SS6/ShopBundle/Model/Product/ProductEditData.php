<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Form\UrlListType;
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
	 * @var \SS6\ShopBundle\Component\Image\Image[]
	 */
	public $imagesToDelete;

	/**
	 * @var \SS6\ShopBundle\Component\Image\Image[imageId]
	 */
	public $imagePositions;

	/**
	 * @var string[pricingGroupId]
	 */
	public $manualInputPrices;

	/**
	 * @var string[domainId]
	 */
	public $seoTitles;

	/**
	 * @var string[domainId]
	 */
	public $seoMetaDescriptions;

	/**
	 * @var string[domainId]
	 */
	public $descriptions;

	/**
	 * @var array
	 */
	public $urls;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 */
	public $accessories;

	/**
	 * @var string[domainId]
	 */
	public $heurekaCpcValues;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 */
	public $variants;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $parameters
	 * @param string[] $imagesToUpload
	 * @param \SS6\ShopBundle\Component\Image\Image[] $imagesToDelete
	 * @param \SS6\ShopBundle\Component\Image\Image[] $imagePositions
	 * @param string[] $manualInputPrices
	 * @param string[] $seoTitles
	 * @param string[] $seoMetaDescriptions
	 * @param string[] $descriptions
	 * @param \SS6\ShopBundle\Model\Product\Product[] $accessories
	 * @param string[] $heurekaCpcValues
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 */
	public function __construct(
		ProductData $productData = null,
		array $parameters = [],
		array $imagesToUpload = [],
		array $imagesToDelete = [],
		array $imagePositions = [],
		array $manualInputPrices = [],
		array $seoTitles = [],
		array $seoMetaDescriptions = [],
		array $descriptions = [],
		array $accessories = [],
		array $heurekaCpcValues = [],
		array $variants = []
	) {
		if ($productData !== null) {
			$this->productData = $productData;
		} else {
			$this->productData = new ProductData();
		}
		$this->parameters = $parameters;
		$this->imagesToUpload = $imagesToUpload;
		$this->imagesToDelete = $imagesToDelete;
		$this->imagePositions = $imagePositions;
		$this->manualInputPrices = $manualInputPrices;
		$this->seoTitles = $seoTitles;
		$this->seoMetaDescriptions = $seoMetaDescriptions;
		$this->descriptions = $descriptions;
		$this->urls = [
			UrlListType::TO_DELETE => [],
			UrlListType::MAIN_ON_DOMAINS => [],
			UrlListType::NEW_URLS => [],
		];
		$this->accessories = $accessories;
		$this->heurekaCpcValues = $heurekaCpcValues;
		$this->variants = $variants;
	}

}
