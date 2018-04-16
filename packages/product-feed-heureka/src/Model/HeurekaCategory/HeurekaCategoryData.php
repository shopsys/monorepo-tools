<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

class HeurekaCategoryData
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $fullName;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public $categories;

    public function __construct()
    {
        $this->categories = [];
    }
}
