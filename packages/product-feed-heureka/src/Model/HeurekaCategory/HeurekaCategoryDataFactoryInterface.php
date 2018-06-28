<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

interface HeurekaCategoryDataFactoryInterface
{
    /**
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryData
     */
    public function create(): HeurekaCategoryData;
}
