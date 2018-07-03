<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\Product;

interface GoogleProductDomainDataFactoryInterface
{
    /**
     * @return \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData
     */
    public function create(): GoogleProductDomainData;
}
