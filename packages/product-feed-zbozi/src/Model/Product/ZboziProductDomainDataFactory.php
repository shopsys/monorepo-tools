<?php

namespace Shopsys\ProductFeed\ZboziBundle\Model\Product;

class ZboziProductDomainDataFactory implements ZboziProductDomainDataFactoryInterface
{
    /**
     * @return \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainData
     */
    public function create(): ZboziProductDomainData
    {
        return new ZboziProductDomainData();
    }
}
