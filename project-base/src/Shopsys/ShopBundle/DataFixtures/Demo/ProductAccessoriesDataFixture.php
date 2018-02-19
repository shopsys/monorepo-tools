<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;

class ProductAccessoriesDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $productEditDataFactory = $this->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productFacade = $this->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\ShopBundle\Model\Product\Product */

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->accessories = [
            $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '24'),
            $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '13'),
        ];
        $productFacade->edit($product->getId(), $productEditData);
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
