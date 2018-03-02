<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $manualBestsellingProductFacade = $this
            ->get(ManualBestsellingProductFacade::class);
        /* @var $manualBestsellingProductFacade \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade */

        $domainId = 2;
        $manualBestsellingProductFacade->edit(
            $this->getReference(DemoCategoryDataFixture::CATEGORY_PHOTO),
            $domainId,
            [$this->getReference(DemoProductDataFixture::PRODUCT_PREFIX . '7')]
        );
    }
}
