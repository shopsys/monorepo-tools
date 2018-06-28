<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData;

class BrandData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var \Shopsys\FrameworkBundle\Component\FileUpload\ImageUploadData
     */
    public $image;

    /**
     * @var string[]
     */
    public $descriptions;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\UrlListData
     */
    public $urls;

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

    public function __construct()
    {
        $this->name = '';
        $this->image = new ImageUploadData();
        $this->descriptions = [];
        $this->urls = new UrlListData();
        $this->seoTitles = [];
        $this->seoMetaDescriptions = [];
        $this->seoH1s = [];
    }
}
