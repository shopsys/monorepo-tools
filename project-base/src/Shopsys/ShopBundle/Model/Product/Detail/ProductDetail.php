<?php

namespace SS6\ShopBundle\Model\Product\Detail;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\Pricing\ProductPrice;
use SS6\ShopBundle\Model\Product\Product;

class ProductDetail {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	private $basePriceForAutoPriceCalculationType;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPrice|null
	 */
	private $sellingPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductDomain[]|null
	 */
	private $productDomainsIndexedByDomainId;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]|null
	 */
	private $parameters;

	/**
	 * @var \SS6\ShopBundle\Component\Image\Image[]|null
	 */
	private $imagesById;

	/**
	 * @var bool
	 */
	private $sellingPriceLoaded;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory $productDetailFactory
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $basePriceForAutoPriceCalculationType
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPrice|null $sellingPrice
	 * @param \SS6\ShopBundle\Model\Product\ProductDomain[]|null $productDomainsIndexedByDomainId
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]|null $parameters
	 * @param \SS6\ShopBundle\Component\Image\Image[imageId]|null $imagesById
	 */
	public function __construct(
		Product $product,
		ProductDetailFactory $productDetailFactory,
		Price $basePriceForAutoPriceCalculationType = null,
		ProductPrice $sellingPrice = null,
		array $productDomainsIndexedByDomainId = null,
		array $parameters = null,
		array $imagesById = null
	) {
		$this->product = $product;
		$this->productDetailFactory = $productDetailFactory;
		$this->basePriceForAutoPriceCalculationType = $basePriceForAutoPriceCalculationType;
		$this->sellingPrice = $sellingPrice;
		$this->productDomainsIndexedByDomainId = $productDomainsIndexedByDomainId;
		$this->parameters = $parameters;
		$this->imagesById = $imagesById;
		$this->sellingPriceLoaded = false;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getBasePriceForAutoPriceCalculationType() {
		if ($this->basePriceForAutoPriceCalculationType === null) {
			$this->basePriceForAutoPriceCalculationType = $this->productDetailFactory
				->getBasePriceForAutoPriceCalculationType($this->product);
		}

		return $this->basePriceForAutoPriceCalculationType;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductPrice|null
	 */
	public function getSellingPrice() {
		if (!$this->sellingPriceLoaded) {
			$this->sellingPrice = $this->productDetailFactory->getSellingPrice($this->product);
			$this->sellingPriceLoaded = true;
		}

		return $this->sellingPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductDomain[]
	 */
	public function getProductDomainsIndexedByDomainId() {
		if ($this->productDomainsIndexedByDomainId === null) {
			$this->productDomainsIndexedByDomainId = $this->productDetailFactory->getProductDomainsIndexedByDomainId($this->product);
		}

		return $this->productDomainsIndexedByDomainId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
	 */
	public function getParameters() {
		if ($this->parameters === null) {
			$this->parameters = $this->productDetailFactory->getParameters($this->product);
		}

		return $this->parameters;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Image\Image[]
	 */
	public function getImagesIndexedById() {
		if ($this->imagesById === null) {
			$this->imagesById = $this->productDetailFactory->getImagesIndexedById($this->product);
		}

		return $this->imagesById;
	}

	/**
	 * @return bool
	 */
	public function hasContentForDetailBox() {
		if ($this->product->isMainVariant()) {
			if ($this->product->getBrand() !== null) {
				return true;
			}
		} else {
			return $this->product->getBrand() !== null
				|| $this->product->getCatnum() !== null
				|| $this->product->getPartno() !== null
				|| $this->product->getEan() !== null;
		}

		return false;
	}

}
