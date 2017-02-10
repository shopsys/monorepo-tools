<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $manualBestsellingProductFacade = $this->get(ManualBestsellingProductFacade::class);
        /* @var $manualBestsellingProductFacade \Shopsys\ShopBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade */

        $domainId = 2;
        $manualBestsellingProductFacade->edit(
            $this->getReference(DemoCategoryDataFixture::PREFIX . DemoCategoryDataFixture::PHOTO),
            $domainId,
            [$this->getReference(DemoProductDataFixture::PRODUCT_PREFIX . '7')]
        );
    }
}
