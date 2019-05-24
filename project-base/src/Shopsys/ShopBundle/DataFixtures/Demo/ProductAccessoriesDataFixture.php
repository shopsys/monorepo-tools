<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;

class ProductAccessoriesDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /** @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface */
    protected $productDataFactory;

    /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
    protected $productFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface $productDataFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     */
    public function __construct(
        ProductDataFactoryInterface $productDataFactory,
        ProductFacade $productFacade
    ) {
        $this->productDataFactory = $productDataFactory;
        $this->productFacade = $productFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $product \Shopsys\FrameworkBundle\Model\Product\Product */

        $productData = $this->productDataFactory->createFromProduct($product);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Product $product24 */
        $product24 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '24');
        /** @var \Shopsys\FrameworkBundle\Model\Product\Product $product13 */
        $product13 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '13');
        $productData->accessories = [
            $product24,
            $product13,
        ];
        $this->productFacade->edit($product->getId(), $productData);
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
