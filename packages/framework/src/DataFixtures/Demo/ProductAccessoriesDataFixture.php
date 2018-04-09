<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;

class ProductAccessoriesDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /** @var \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
    private $productEditDataFactory;

    /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
    private $productFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory $productEditDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     */
    public function __construct(
        ProductEditDataFactory $productEditDataFactory,
        ProductFacade $productFacade
    ) {
        $this->productEditDataFactory = $productEditDataFactory;
        $this->productFacade = $productFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productEditData = $this->productEditDataFactory->createFromProduct($product);
        $productEditData->accessories = [
            $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '24'),
            $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '13'),
        ];
        $this->productFacade->edit($product->getId(), $productEditData);
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
