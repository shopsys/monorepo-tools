<?php

namespace Tests\ShopBundle\Database\Model\Product;

use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ProductSellingDeniedRecalculatorTest extends DatabaseTestCase
{
    public function testCalculateSellingDeniedForProductSellableVariant()
    {
        $em = $this->getEntityManager();
        $productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\ShopBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\ShopBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\ShopBundle\Model\Product\Product */

        $variant1productData = $productDataFactory->createFromProduct($variant1);
        $variant1productData->sellingDenied = true;
        $productFacade->edit($variant1->getId(), $variant1productData);

        $productSellingDeniedRecalculator->calculateSellingDeniedForProduct($variant1);

        $em->refresh($variant1);
        $em->refresh($variant2);
        $em->refresh($variant3);
        $em->refresh($mainVariant);

        $this->assertTrue($variant1->getCalculatedSellingDenied());
        $this->assertFalse($variant2->getCalculatedSellingDenied());
        $this->assertFalse($variant3->getCalculatedSellingDenied());
        $this->assertFalse($mainVariant->getCalculatedSellingDenied());
    }

    public function testCalculateSellingDeniedForProductNotSellableVariants()
    {
        $em = $this->getEntityManager();
        $productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\ShopBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */

        $variant1productData = $productDataFactory->createFromProduct($variant1);
        $variant1productData->sellingDenied = true;
        $productFacade->edit($variant1->getId(), $variant1productData);
        $variant2productData = $productDataFactory->createFromProduct($variant2);
        $variant2productData->sellingDenied = true;
        $productFacade->edit($variant2->getId(), $variant2productData);
        $variant3productData = $productDataFactory->createFromProduct($variant3);
        $variant3productData->sellingDenied = true;
        $productFacade->edit($variant3->getId(), $variant3productData);

        $productSellingDeniedRecalculator->calculateSellingDeniedForProduct($mainVariant);

        $em->refresh($variant1);
        $em->refresh($variant2);
        $em->refresh($variant3);
        $em->refresh($mainVariant);

        $this->assertTrue($variant1->getCalculatedSellingDenied());
        $this->assertTrue($variant2->getCalculatedSellingDenied());
        $this->assertTrue($variant3->getCalculatedSellingDenied());
        $this->assertTrue($mainVariant->getCalculatedSellingDenied());
    }

    public function testCalculateSellingDeniedForProductNotSellableMainVariant()
    {
        $em = $this->getEntityManager();
        $productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\ShopBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\ShopBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\ShopBundle\Model\Product\Product */

        $mainVariantproductData = $productDataFactory->createFromProduct($mainVariant);
        $mainVariantproductData->sellingDenied = true;
        $productFacade->edit($mainVariant->getId(), $mainVariantproductData);

        $productSellingDeniedRecalculator->calculateSellingDeniedForProduct($mainVariant);

        $em->refresh($variant1);
        $em->refresh($variant2);
        $em->refresh($variant3);
        $em->refresh($mainVariant);

        $this->assertTrue($variant1->getCalculatedSellingDenied());
        $this->assertTrue($variant2->getCalculatedSellingDenied());
        $this->assertTrue($variant3->getCalculatedSellingDenied());
        $this->assertTrue($mainVariant->getCalculatedSellingDenied());
    }
}
