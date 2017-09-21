<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;

class TopProductDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $topProductReferenceNames = [
            ProductDataFixture::PRODUCT_PREFIX . '1',
            ProductDataFixture::PRODUCT_PREFIX . '17',
            ProductDataFixture::PRODUCT_PREFIX . '9',
        ];

        $this->createTopProducts($topProductReferenceNames);
    }

    /**
     * @param string[] $productReferenceNames
     */
    private function createTopProducts(array $productReferenceNames)
    {
        $topProductFacade = $this->get('shopsys.shop.product.top_product.top_product_facade');
        /* @var $topProductFacade \Shopsys\ShopBundle\Model\Product\TopProduct\TopProductFacade */

        $products = [];
        foreach ($productReferenceNames as $productReferenceName) {
            $products[] = $this->getReference($productReferenceName);
        }

        $topProductFacade->saveTopProductsForDomain(Domain::FIRST_DOMAIN_ID, $products);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            ProductDataFixture::class,
        ];
    }
}
