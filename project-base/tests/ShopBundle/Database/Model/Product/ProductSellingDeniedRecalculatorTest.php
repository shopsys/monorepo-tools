<?php

namespace Tests\ShopBundle\Database\Model\Product;

use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ProductSellingDeniedRecalculatorTest extends DatabaseTestCase
{
    public function testCalculateSellingDeniedForProductSellableVariant()
    {
        $em = $this->getEntityManager();
        $productSellingDeniedRecalculator = $this->getServiceByType(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\FrameworkBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\FrameworkBundle\Model\Product\Product */

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
        $productSellingDeniedRecalculator = $this->getServiceByType(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */

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
        $productSellingDeniedRecalculator = $this->getServiceByType(ProductSellingDeniedRecalculator::class);
        /* @var $productSellingDeniedRecalculator \Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator */
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\FrameworkBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\FrameworkBundle\Model\Product\Product */

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
