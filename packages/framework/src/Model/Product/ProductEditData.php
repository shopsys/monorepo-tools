<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Form\UrlListData;

class ProductEditData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public $productData;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[]
     */
    public $parameters;

    /**
     * @var string[]
     */
    public $imagesToUpload;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public $imagesToDelete;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\Image[]
     */
    public $orderedImagesById;

    /**
     * @var string[]
     */
    public $manualInputPricesByPricingGroupId;

    /**
     * @var string[]|null[]
     */
    public $seoTitles;

    /**
     * @var string[]|null[]
     */
    public $seoMetaDescriptions;

    /**
     * @var string[]|null[]
     */
    public $descriptions;

    /**
     * @var string[]|null[]
     */
    public $shortDescriptions;

    /**
     * @var \Shopsys\FrameworkBundle\Form\UrlListData
     */
    public $urls;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public $accessories;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public $variants;

    /**
     * @var string[]|null[]
     */
    public $seoH1s;

    /**
     * @var array
     */
    public $pluginData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param \Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValueData[] $parameters
     * @param string[] $imagesToUpload
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $imagesToDelete
     * @param \Shopsys\FrameworkBundle\Component\Image\Image[] $orderedImagesById
     * @param string[] $manualInputPricesByPricingGroupId
     * @param string[]|null[] $seoTitles
     * @param string[]|null[] $seoMetaDescriptions
     * @param string[]|null[] $descriptions
     * @param string[]|null[] $shortDescriptions
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $accessories
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @param string[]|null[] $seoH1s
     */
    public function __construct(
        ProductData $productData = null,
        array $parameters = [],
        array $imagesToUpload = [],
        array $imagesToDelete = [],
        array $orderedImagesById = [],
        array $manualInputPricesByPricingGroupId = [],
        array $seoTitles = [],
        array $seoMetaDescriptions = [],
        array $descriptions = [],
        array $shortDescriptions = [],
        array $accessories = [],
        array $variants = [],
        array $seoH1s = []
    ) {
        if ($productData !== null) {
            $this->productData = $productData;
        } else {
            $this->productData = new ProductData();
        }
        $this->parameters = $parameters;
        $this->imagesToUpload = $imagesToUpload;
        $this->imagesToDelete = $imagesToDelete;
        $this->orderedImagesById = $orderedImagesById;
        $this->manualInputPricesByPricingGroupId = $manualInputPricesByPricingGroupId;
        $this->seoTitles = $seoTitles;
        $this->seoMetaDescriptions = $seoMetaDescriptions;
        $this->descriptions = $descriptions;
        $this->shortDescriptions = $shortDescriptions;
        $this->urls = new UrlListData();
        $this->accessories = $accessories;
        $this->variants = $variants;
        $this->seoH1s = $seoH1s;
        $this->pluginData = [];
    }
}
