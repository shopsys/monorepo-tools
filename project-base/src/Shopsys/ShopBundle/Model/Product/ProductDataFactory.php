<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\Vat\VatFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\Unit\UnitFacade;

class ProductDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\VatFacade
	 */
	private $vatFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceFacade
	 */
	private $productInputPriceFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\UnitFacade
	 */
	private $unitFacade;

	public function __construct(
		VatFacade $vatFacade,
		ProductInputPriceFacade $productInputPriceFacade,
		UnitFacade $unitFacade
	) {
		$this->vatFacade = $vatFacade;
		$this->productInputPriceFacade = $productInputPriceFacade;
		$this->unitFacade = $unitFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function createDefault() {
		$productData = new ProductData();

		$productData->vat = $this->vatFacade->getDefaultVat();
		$productData->unit = $this->unitFacade->getDefaultUnit();

		return $productData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @return \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function createFromProduct(Product $product) {
		$productData = $this->createDefault();

		$translations = $product->getTranslations();
		$names = [];
		$variantAliases = [];
		foreach ($translations as $translation) {
			$names[$translation->getLocale()] = $translation->getName();
			$variantAliases[$translation->getLocale()] = $translation->getVariantAlias();
		}
		$productData->name = $names;
		$productData->variantAlias = $variantAliases;

		$productData->catnum = $product->getCatnum();
		$productData->partno = $product->getPartno();
		$productData->ean = $product->getEan();
		$productData->price = $this->productInputPriceFacade->getInputPrice($product);
		$productData->vat = $product->getVat();
		$productData->sellingFrom = $product->getSellingFrom();
		$productData->sellingTo = $product->getSellingTo();
		$productData->sellingDenied = $product->isSellingDenied();
		$productData->flags = $product->getFlags()->toArray();
		$productData->usingStock = $product->isUsingStock();
		$productData->stockQuantity = $product->getStockQuantity();
		$productData->unit = $product->getUnit();
		$productData->availability = $product->getAvailability();
		$productData->outOfStockAvailability = $product->getOutOfStockAvailability();
		$productData->outOfStockAction = $product->getOutOfStockAction();

		$productData->hidden = $product->isHidden();

		$productData->categoriesByDomainId = $product->getCategoriesIndexedByDomainId();
		$productData->priceCalculationType = $product->getPriceCalculationType();
		$productData->brand = $product->getBrand();
		$productData->orderingPriority = $product->getOrderingPriority();

		return $productData;
	}

}
