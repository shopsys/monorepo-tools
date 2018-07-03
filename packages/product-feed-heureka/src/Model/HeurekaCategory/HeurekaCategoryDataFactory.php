<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory;

class HeurekaCategoryDataFactory implements HeurekaCategoryDataFactoryInterface
{
    /**
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryData
     */
    public function create(): HeurekaCategoryData
    {
        return new HeurekaCategoryData();
    }
}
