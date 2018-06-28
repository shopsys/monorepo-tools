<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\Product;

class GoogleProductDomainDataFactory implements GoogleProductDomainDataFactoryInterface
{
    /**
     * @return \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData
     */
    public function create(): GoogleProductDomainData
    {
        return new GoogleProductDomainData();
    }
}
