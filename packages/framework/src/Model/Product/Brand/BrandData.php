<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand;

use Shopsys\FrameworkBundle\Form\UrlListData;

class BrandData
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string[]
     */
    public $image;

    /**
     * @var string[]
     */
    public $descriptions;

    /**
     * @var \Shopsys\FrameworkBundle\Form\UrlListData
     */
    public $urls;

    public function __construct()
    {
        $this->name = '';
        $this->image = [];
        $this->descriptions = [];
        $this->urls = new UrlListData();
    }
}
