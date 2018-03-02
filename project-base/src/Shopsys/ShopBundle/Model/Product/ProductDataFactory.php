<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade;
use Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade;

class ProductDataFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    private $vatFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceFacade
     */
    private $productInputPriceFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\UnitFacade
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
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createDefault()
    {
        $productData = new ProductData();

        $productData->vat = $this->vatFacade->getDefaultVat();
        $productData->unit = $this->unitFacade->getDefaultUnit();

        return $productData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromProduct(Product $product)
    {
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
