<?php

namespace Tests\ShopBundle\Database\Model\Product;

use DateTime;
use Shopsys\FrameworkBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\PricingGroupDataFixture as DemoPricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ProductVisibilityRepositoryTest extends DatabaseTestCase
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    private function getDefaultProductData()
    {
        $category = $this->getReference(CategoryDataFixture::CATEGORY_ELECTRONICS);

        $em = $this->getEntityManager();
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);
        $em->persist($vat);

        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */

        $productData = $productDataFactory->createDefault();
        $productData->name = ['cs' => 'Name', 'en' => 'Name'];
        $productData->vat = $vat;
        $productData->price = 100;
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $productData->categoriesByDomainId = [1 => [$category]];
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        $productData->unit = $this->getReference(UnitDataFixture::UNIT_PIECES);

        return $productData;
    }

    public function testIsVisibleOnAnyDomainWhenHidden()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productData = $this->getDefaultProductData();
        $productData->hidden = true;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\FrameworkBundle\Model\Product\Product */

        $productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $productAgain,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility1 \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productAgain->isVisible());
        $this->assertFalse($productVisibility1->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenNotHidden()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productData = $this->getDefaultProductData();
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\FrameworkBundle\Model\Product\Product */

        $productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $productAgain->getId(),
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility1 \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertTrue($productAgain->isVisible());
        $this->assertTrue($productVisibility1->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingInFuture()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $sellingFrom = new DateTime('now');
        $sellingFrom->modify('+1 day');

        $productData = $this->getDefaultProductData();
        $productData->sellingFrom = $sellingFrom;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\FrameworkBundle\Model\Product\Product */

        $this->assertFalse($productAgain->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingInPast()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $sellingTo = new DateTime('now');
        $sellingTo->modify('-1 day');

        $productData = $this->getDefaultProductData();
        $productData->sellingTo = $sellingTo;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\FrameworkBundle\Model\Product\Product */

        $this->assertFalse($productAgain->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingNow()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $sellingFrom = new DateTime('now');
        $sellingFrom->modify('-1 day');
        $sellingTo = new DateTime('now');
        $sellingTo->modify('+1 day');

        $productData = $this->getDefaultProductData();
        $productData->sellingFrom = $sellingFrom;
        $productData->sellingTo = $sellingTo;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productAgain = $em->getRepository(Product::class)->find($id);
        /* @var $productAgain \Shopsys\FrameworkBundle\Model\Product\Product */

        $this->assertTrue($productAgain->isVisible());
    }

    public function testIsNotVisibleWhenZeroOrNullPrice()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productData = $this->getDefaultProductData();
        $productData->price = 0;
        $product1 = $productFacade->create($productData);

        $productData->price = null;
        $product2 = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $product1Id = $product1->getId();
        $product2Id = $product2->getId();
        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $product1Again = $em->getRepository(Product::class)->find($product1Id);
        /* @var $product1Again \Shopsys\FrameworkBundle\Model\Product\Product */
        $product2Again = $em->getRepository(Product::class)->find($product2Id);
        /* @var $product2Again \Shopsys\FrameworkBundle\Model\Product\Product */

        $this->assertFalse($product1Again->isVisible());
        $this->assertFalse($product2Again->isVisible());
    }

    public function testIsVisibleWithFilledName()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productData = $this->getDefaultProductData();
        $productData->name = ['cs' => 'Name', 'en' => 'Name'];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertTrue($productVisibility->isVisible());
    }

    public function testIsNotVisibleWithEmptyName()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productData = $this->getDefaultProductData();
        $productData->name = ['cs' => null, 'en' => null];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsVisibleInVisibileCategory()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $category = $this->getReference(CategoryDataFixture::CATEGORY_TOYS);

        $productData = $this->getDefaultProductData();
        $productData->categoriesByDomainId = [1 => [$category]];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertTrue($productVisibility->isVisible());
    }

    public function testIsNotVisibleInHiddenCategory()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */

        $productData = $this->getDefaultProductData();
        $productData->categoriesByDomainId = [];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId(),
            'domainId' => 1,
        ]);

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsNotVisibleWhenZeroManualPrice()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */

        $productData = $this->getDefaultProductData();
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;

        $allPricingGroups = $pricingGroupFacade->getAll();
        foreach ($allPricingGroups as $pricingGroup) {
            $productData->manualInputPricesByPricingGroupId[$pricingGroup->getId()] = 10;
        }

        $pricingGroupWithZeroPriceId = $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId();

        $productData->manualInputPricesByPricingGroupId[$pricingGroupWithZeroPriceId] = 0;

        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroupWithZeroPriceId,
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsNotVisibleWhenNullManualPrice()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */

        $productData = $this->getDefaultProductData();
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;

        $allPricingGroups = $pricingGroupFacade->getAll();
        foreach ($allPricingGroups as $pricingGroup) {
            $productData->manualInputPricesByPricingGroupId[$pricingGroup->getId()] = 10;
        }

        $pricingGroupWithNullPriceId = $this->getReference(DemoPricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1)->getId();
        $productData->manualInputPricesByPricingGroupId[$pricingGroupWithNullPriceId] = null;

        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productVisibilityRepository->refreshProductsVisibility();

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroupWithNullPriceId,
            'domainId' => 1,
        ]);
        /* @var $productVisibility \Shopsys\FrameworkBundle\Model\Product\ProductVisibility */

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testRefreshProductsVisibilityVisibleVariants()
    {
        $em = $this->getEntityManager();
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\FrameworkBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\FrameworkBundle\Model\Product\Product */

        $variant1productData = $productDataFactory->createFromProduct($variant1);
        $variant1productData->hidden = true;
        $productFacade->edit($variant1->getId(), $variant1productData);

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

    public function testRefreshProductsVisibilityNotVisibleVariants()
    {
        $em = $this->getEntityManager();
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\FrameworkBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\FrameworkBundle\Model\Product\Product */

        $variant1productData = $productDataFactory->createFromProduct($variant1);
        $variant1productData->hidden = true;
        $productFacade->edit($variant1->getId(), $variant1productData);

        $variant2productData = $productDataFactory->createFromProduct($variant2);
        $variant2productData->hidden = true;
        $productFacade->edit($variant2->getId(), $variant2productData);

        $variant3productData = $productDataFactory->createFromProduct($variant3);
        $variant3productData->hidden = true;
        $productFacade->edit($variant3->getId(), $variant3productData);

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

    public function testRefreshProductsVisibilityNotVisibleMainVariant()
    {
        $em = $this->getEntityManager();
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /* @var $productVisibilityRepository \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactory::class);
        /* @var $productDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory */

        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /* @var $variant1 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /* @var $variant2 \Shopsys\FrameworkBundle\Model\Product\Product */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /* @var $variant3 \Shopsys\FrameworkBundle\Model\Product\Product */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');
        /* @var $mainVariant \Shopsys\FrameworkBundle\Model\Product\Product */

        $mainVariantproductData = $productDataFactory->createFromProduct($mainVariant);
        $mainVariantproductData->hidden = true;
        $productFacade->edit($mainVariant->getId(), $mainVariantproductData);

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
