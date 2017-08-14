<?php

namespace Shopsys\ShopBundle\Model\Product\Brand;

use Shopsys\ShopBundle\Form\UrlListData;
use Shopsys\ShopBundle\Model\Product\Brand\Brand;

class BrandEditData
{
    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\BrandData|null
     */
    public $brandData;

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
    public $seoH1s;

    public function __construct(
        BrandData $brandData = null,
        array $seoTitles = [],
        array $seoMetaDescriptions = [],
        array $seoH1s = []
    ) {
        if ($brandData !== null) {
            $this->brandData = $brandData;
        } else {
            $this->brandData = new BrandData();
        }
        $this->seoTitles = $seoTitles;
        $this->seoMetaDescriptions = $seoMetaDescriptions;
        $this->seoH1s = $seoH1s;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Brand\BrandData|null
     */
    public function getBrandData()
    {
        return $this->brandData;
    }
}
