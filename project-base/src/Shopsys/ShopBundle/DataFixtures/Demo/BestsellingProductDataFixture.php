<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade;

class BestsellingProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /** @var \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade */
    protected $manualBestsellingProductFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProductFacade $manualBestsellingProductFacade
     */
    public function __construct(
        ManualBestsellingProductFacade $manualBestsellingProductFacade
    ) {
        $this->manualBestsellingProductFacade = $manualBestsellingProductFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manualBestsellingProductFacade->edit(
            $this->getReference(CategoryDataFixture::CATEGORY_PHOTO),
            Domain::FIRST_DOMAIN_ID,
            [
                0 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '7'),
                2 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '8'),
                8 => $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '5'),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            ProductDataFixture::class,
            CategoryDataFixture::class,
        ];
    }
}
