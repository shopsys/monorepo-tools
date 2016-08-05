<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Form\UrlListData;
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
	 * @var string[domainId]
	 */
	public $shortDescriptions;

	/**
	 * @var \SS6\ShopBundle\Form\UrlListData
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
	 * @var bool[domainId]
	 */
	public $showInZboziFeed;

	/**
	 * @var string[domainId]
	 */
	public $zboziCpcValues;

	/**
	 * @var string[domainId]
	 */
	public $zboziCpcSearchValues;

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
	 * @param string[] $shortDescriptions
	 * @param \SS6\ShopBundle\Model\Product\Product[] $accessories
	 * @param string[] $heurekaCpcValues
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 * @param bool[] $showInZboziFeed
	 * @param string[] $zboziCpcValues
	 * @param string[] $zboziCpcSearchValues
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
		array $shortDescriptions = [],
		array $accessories = [],
		array $heurekaCpcValues = [],
		array $variants = [],
		array $showInZboziFeed = [],
		array $zboziCpcValues = [],
		array $zboziCpcSearchValues = []
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
		$this->shortDescriptions = $shortDescriptions;
		$this->urls = new UrlListData();
		$this->accessories = $accessories;
		$this->heurekaCpcValues = $heurekaCpcValues;
		$this->variants = $variants;
		$this->showInZboziFeed = $showInZboziFeed;
		$this->zboziCpcValues = $zboziCpcValues;
		$this->zboziCpcSearchValues = $zboziCpcSearchValues;
	}

}
