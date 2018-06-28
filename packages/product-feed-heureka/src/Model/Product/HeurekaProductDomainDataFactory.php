<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

class HeurekaProductDomainDataFactory implements HeurekaProductDomainDataFactoryInterface
{
    /**
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainData
     */
    public function create(): HeurekaProductDomainData
    {
        return new HeurekaProductDomainData();
    }
}
