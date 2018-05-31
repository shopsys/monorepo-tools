<?php

namespace Shopsys\FrameworkBundle\Model\Product\Detail;

use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductDetail
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product
     */
    private $product;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory
     */
    private $productDetailFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice|null
     */
    private $sellingPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]|null
     */
    private $parameters;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Image[]|null
     */
    private $imagesById;

    /**
     * @var bool
     */
    private $sellingPriceLoaded;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Detail\ProductDetailFactory $productDetailFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice|null $sellingPrice
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]|null $parameters
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[]|null $imagesById
     */
    public function __construct(
        Product $product,
        ProductDetailFactory $productDetailFactory,
        ProductPrice $sellingPrice = null,
        array $parameters = null,
        array $imagesById = null
    ) {
        $this->product = $product;
        $this->productDetailFactory = $productDetailFactory;
        $this->sellingPrice = $sellingPrice;
        $this->parameters = $parameters;
        $this->imagesById = $imagesById;
        $this->sellingPriceLoaded = false;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice|null
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
     * @return \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue[]
     */
    public function getParameters()
    {
        if ($this->parameters === null) {
            $this->parameters = $this->productDetailFactory->getParameters($this->product);
        }

        return $this->parameters;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Image\Image[]
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
