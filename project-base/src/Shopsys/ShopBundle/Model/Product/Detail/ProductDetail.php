<?php

namespace Shopsys\ShopBundle\Model\Product\Detail;

use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductDetail
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product
     */
    private $product;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory
     */
    private $productDetailFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Pricing\Price|null
     */
    private $basePriceForAutoPriceCalculationType;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice|null
     */
    private $sellingPrice;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductDomain[]|null
     */
    private $productDomainsIndexedByDomainId;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue[]|null
     */
    private $parameters;

    /**
     * @var \Shopsys\ShopBundle\Component\Image\Image[]|null
     */
    private $imagesById;

    /**
     * @var bool
     */
    private $sellingPriceLoaded;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory $productDetailFactory
     * @param \Shopsys\ShopBundle\Model\Pricing\Price|null $basePriceForAutoPriceCalculationType
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice|null $sellingPrice
     * @param \Shopsys\ShopBundle\Model\Product\ProductDomain[]|null $productDomainsIndexedByDomainId
     * @param \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue[]|null $parameters
     * @param \Shopsys\ShopBundle\Component\Image\Image[imageId]|null $imagesById
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
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Pricing\Price
     */
    public function getBasePriceForAutoPriceCalculationType()
    {
        if ($this->basePriceForAutoPriceCalculationType === null) {
            $this->basePriceForAutoPriceCalculationType = $this->productDetailFactory
                ->getBasePriceForAutoPriceCalculationType($this->product);
        }

        return $this->basePriceForAutoPriceCalculationType;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice|null
     */
    public function getSellingPrice()
    {
        if (!$this->sellingPriceLoaded) {
            $this->sellingPrice = $this->productDetailFactory->getSellingPrice($this->product);
            $this->sellingPriceLoaded = true;
        }

        return $this->sellingPrice;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\ProductDomain[]
     */
    public function getProductDomainsIndexedByDomainId()
    {
        if ($this->productDomainsIndexedByDomainId === null) {
            $this->productDomainsIndexedByDomainId = $this->productDetailFactory->getProductDomainsIndexedByDomainId($this->product);
        }

        return $this->productDomainsIndexedByDomainId;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = $this->productDetailFactory->getParameters($this->product);
        }

        return $this->parameters;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Image\Image[]
     */
    public function getImagesIndexedById()
    {
        if ($this->imagesById === null) {
            $this->imagesById = $this->productDetailFactory->getImagesIndexedById($this->product);
        }

        return $this->imagesById;
    }

    /**
     * @return bool
     */
    public function hasContentForDetailBox()
    {
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
