<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Product;

use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductSellingDeniedRecalculatorTest extends DatabaseTestCase
{
    public function testCalculateSellingDeniedForProductSellableVariant()
    {
        $em = $this->getEntityManager();
        $productSellingDeniedRecalculator = $this->getContainer()->get(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\ShopBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\ShopBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\ShopBundle\Model\Product\Product */

        $variant1ProductEditData = $productEditDataFactory->createFromProduct($variant1);
        $variant1ProductEditData->productData->sellingDenied = true;
        $productFacade->edit($variant1->getId(), $variant1ProductEditData);

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
        /* @var $productSellingDeniedRecalculator \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\ShopBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */

        $variant1ProductEditData = $productEditDataFactory->createFromProduct($variant1);
        $variant1ProductEditData->productData->sellingDenied = true;
        $productFacade->edit($variant1->getId(), $variant1ProductEditData);
        $variant2ProductEditData = $productEditDataFactory->createFromProduct($variant2);
        $variant2ProductEditData->productData->sellingDenied = true;
        $productFacade->edit($variant2->getId(), $variant2ProductEditData);
        $variant3ProductEditData = $productEditDataFactory->createFromProduct($variant3);
        $variant3ProductEditData->productData->sellingDenied = true;
        $productFacade->edit($variant3->getId(), $variant3ProductEditData);

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
        /* @var $productSellingDeniedRecalculator \Shopsys\ShopBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\ShopBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\ShopBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\ShopBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\ShopBundle\Model\Product\Product */

        $mainVariantProductEditData = $productEditDataFactory->createFromProduct($mainVariant);
        $mainVariantProductEditData->productData->sellingDenied = true;
        $productFacade->edit($mainVariant->getId(), $mainVariantProductEditData);

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
