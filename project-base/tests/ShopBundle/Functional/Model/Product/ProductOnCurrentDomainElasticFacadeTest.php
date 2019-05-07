<?php

namespace Tests\ShopBundle\Functional\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainElasticFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface;

class ProductOnCurrentDomainElasticFacadeTest extends ProductOnCurrentDomainFacadeTest
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductOnCurrentDomainFacadeInterface
     */
    public function getProductOnCurrentDomainFacade(): ProductOnCurrentDomainFacadeInterface
    {
        return $this->getContainer()->get(ProductOnCurrentDomainElasticFacade::class);
    }
}
