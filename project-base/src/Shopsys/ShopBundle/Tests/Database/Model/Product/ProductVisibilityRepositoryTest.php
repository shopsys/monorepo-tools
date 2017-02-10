<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Product;

use DateTime;
use Shopsys\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\PricingGroupDataFixture as DemoPricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditData;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\ProductVisibility;
use Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductVisibilityRepositoryTest extends DatabaseTestCase
{

    /**
     * @return \Shopsys\ShopBundle\Model\Product\ProductEditData
     */
    private function getDefaultProductEditData() {
        $category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::ELECTRONICS);

        $em = $this->getEntityManager();
        $vat = new Vat(new VatData('vat', 21));
        $em->persist($vat);

        $productEditData = new ProductEditData();
        $productEditData->productData->name = ['cs' => 'Name'];
        $productEditData->productData->vat = $vat;
        $productEditData->productData->price = 100;
        $productEditData->productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $productEditData->productData->hidden = false;
        $productEditData->productData->sellingDenied = false;
        $productEditData->productData->categoriesByDomainId = [1 => [$category]];
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::IN_STOCK);
        $productEditData->productData->unit = $this->getReference(UnitDataFixture::PCS);

        return $productEditData;
    }

    public function testIsVisibleOnAnyDomainWhenHidden() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->hidden = true;
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\ShopBundle\Model\Product\Product */

        $productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $productAgain,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility1 \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productAgain->isVisible());
        $this->assertFalse($productVisibility1->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenNotHidden() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productEditData = $this->getDefaultProductEditData();
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\ShopBundle\Model\Product\Product */

        $productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $productAgain->getId(),
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility1 \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertTrue($productAgain->isVisible());
        $this->assertTrue($productVisibility1->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingInFuture() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $sellingFrom = new DateTime('now');
        $sellingFrom->modify('+1 day');

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->sellingFrom = $sellingFrom;
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\ShopBundle\Model\Product\Product */

        $this->assertFalse($productAgain->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingInPast() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $sellingTo = new DateTime('now');
        $sellingTo->modify('-1 day');

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->sellingTo = $sellingTo;
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\ShopBundle\Model\Product\Product */

        $this->assertFalse($productAgain->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingNow() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $sellingFrom = new DateTime('now');
        $sellingFrom->modify('-1 day');
        $sellingTo = new DateTime('now');
        $sellingTo->modify('+1 day');

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->sellingFrom = $sellingFrom;
        $productEditData->productData->sellingTo = $sellingTo;
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\ShopBundle\Model\Product\Product */

        $this->assertTrue($productAgain->isVisible());
    }

    public function testIsNotVisibleWhenZeroOrNullPrice() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->price = 0;
        $product1 = $productFacade->create($productEditData);

        $productEditData->productData->price = null;
        $product2 = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $product1Id = $product1->getId();
        $product2Id = $product2->getId();
        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $product1Again = $em->getRepository(Product::class)->find($product1Id);
        /* @var $product1Again \Shopsys\ShopBundle\Model\Product\Product */
        $product2Again = $em->getRepository(Product::class)->find($product2Id);
        /* @var $product2Again \Shopsys\ShopBundle\Model\Product\Product */

        $this->assertFalse($product1Again->isVisible());
        $this->assertFalse($product2Again->isVisible());
    }

    public function testIsVisibleWithFilledName() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->name = ['cs' => 'Name'];
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertTrue($productVisibility->isVisible());
    }

    public function testIsNotVisibleWithEmptyName() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->name = ['cs' => null];
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsVisibleInVisibileCategory() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $category = $this->getReference(CategoryDataFixture::PREFIX . CategoryDataFixture::TOYS);

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->categoriesByDomainId = [1 => [$category]];
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertTrue($productVisibility->isVisible());
    }

    public function testIsNotVisibleInHiddenCategory() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->categoriesByDomainId = [];
        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsNotVisibleWhenZeroManualPrice() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;

        $allPricingGroups = $pricingGroupFacade->getAll();
        foreach ($allPricingGroups as $pricingGroup) {
            $productEditData->manualInputPrices[$pricingGroup->getId()] = 10;
        }

        $pricingGroupWithZeroPriceId = $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId();

        $productEditData->manualInputPrices[$pricingGroupWithZeroPriceId] = 0;

        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroupWithZeroPriceId,
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsNotVisibleWhenNullManualPrice() {
        $em = $this->getEntityManager();
        $entityManagerFacade = $this->getEntityManagerFacade();
        /* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

        $productEditData = $this->getDefaultProductEditData();
        $productEditData->productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;

        $allPricingGroups = $pricingGroupFacade->getAll();
        foreach ($allPricingGroups as $pricingGroup) {
            $productEditData->manualInputPrices[$pricingGroup->getId()] = 10;
        }

        $pricingGroupWithNullPriceId = $this->getReference(DemoPricingGroupDataFixture::ORDINARY_DOMAIN_1)->getId();
        $productEditData->manualInputPrices[$pricingGroupWithNullPriceId] = null;

        $product = $productFacade->create($productEditData);
        $productPriceRecalculator->runImmediateRecalculations();

        $entityManagerFacade->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroupWithNullPriceId,
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\ShopBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testRefreshProductsVisibilityVisibleVariants() {
        $em = $this->getEntityManager();
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
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
        $variant1ProductEditData->productData->hidden = true;
        $productFacade->edit($variant1->getId(), $variant1ProductEditData);

        $productVisibilityRepository->refreshProductsVisibility(true);

        $em->refresh($variant1);
        $em->refresh($variant2);
        $em->refresh($variant3);
        $em->refresh($mainVariant);

        $this->assertFalse($variant1->isVisible());
        $this->assertTrue($variant2->isVisible());
        $this->assertTrue($variant3->isVisible());
        $this->assertTrue($mainVariant->isVisible());
    }

    public function testRefreshProductsVisibilityNotVisibleVariants() {
        $em = $this->getEntityManager();
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
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
        $variant1ProductEditData->productData->hidden = true;
        $productFacade->edit($variant1->getId(), $variant1ProductEditData);

        $variant2ProductEditData = $productEditDataFactory->createFromProduct($variant2);
        $variant2ProductEditData->productData->hidden = true;
        $productFacade->edit($variant2->getId(), $variant2ProductEditData);

        $variant3ProductEditData = $productEditDataFactory->createFromProduct($variant3);
        $variant3ProductEditData->productData->hidden = true;
        $productFacade->edit($variant3->getId(), $variant3ProductEditData);

        $productVisibilityRepository->refreshProductsVisibility(true);

        $em->refresh($variant1);
        $em->refresh($variant2);
        $em->refresh($variant3);
        $em->refresh($mainVariant);

        $this->assertFalse($variant1->isVisible());
        $this->assertFalse($variant2->isVisible());
        $this->assertFalse($variant3->isVisible());
        $this->assertFalse($mainVariant->isVisible());
    }

    public function testRefreshProductsVisibilityNotVisibleMainVariant() {
        $em = $this->getEntityManager();
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\ShopBundle\Model\Product\ProductVisibilityRepository */
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
        $mainVariantProductEditData->productData->hidden = true;
        $productFacade->edit($mainVariant->getId(), $mainVariantProductEditData);

        $productVisibilityRepository->refreshProductsVisibility(true);

        $em->refresh($variant1);
        $em->refresh($variant2);
        $em->refresh($variant3);
        $em->refresh($mainVariant);

        $this->assertFalse($variant1->isVisible());
        $this->assertFalse($variant2->isVisible());
        $this->assertFalse($variant3->isVisible());
        $this->assertFalse($mainVariant->isVisible());
    }
}
