<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture as DemoCategoryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture as DemoProductDataFixture;
use Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade
     */
    private $manualBestsellingProductFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(ManualBestsellingProductFacade $manualBestsellingProductFacade, Domain $domain)
    {
        $this->manualBestsellingProductFacade = $manualBestsellingProductFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    private function loadForDomain(int $domainId)
    {
        $this->manualBestsellingProductFacade->edit(
            $this->getReference(DemoCategoryDataFixture::CATEGORY_PHOTO),
            $domainId,
            [$this->getReference(DemoProductDataFixture::PRODUCT_PREFIX . '7')]
        );
    }
}
