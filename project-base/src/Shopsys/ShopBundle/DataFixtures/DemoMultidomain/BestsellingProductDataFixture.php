<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;

class BestsellingProductDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manualBestsellingProductFacade = $this
            ->get('shopsys.shop.product.bestselling_product.manual_bestselling_product_facade');
        /* @var $manualBestsellingProductFacade \Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade */

        $domainId = 2;
        $manualBestsellingProductFacade->edit(
            $this->getReference(DemoCategoryDataFixture::CATEGORY_PHOTO),
            $domainId,
            [$this->getReference(DemoProductDataFixture::PRODUCT_PREFIX . '7')]
        );
    }
}
