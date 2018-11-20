<?php

namespace Tests\ShopBundle\Functional\Model\Product;

use DateTime;
use Shopsys\FrameworkBundle\DataFixtures\Demo\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\CategoryDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibility;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class ProductVisibilityRepositoryTest extends TransactionFunctionalTestCase
{
    /**
     * @return \Shopsys\ShopBundle\Model\Product\ProductData
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

        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        $productData = $productDataFactory->create();
        $productData->name = ['cs' => 'Name', 'en' => 'Name'];
        $productData->vat = $vat;
        $productData->categoriesByDomainId = [1 => [$category]];
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        $productData->unit = $this->getReference(UnitDataFixture::UNIT_PIECES);
        $this->setPriceForAllDomains($productData, 100);

        return $productData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param $price
     */
    private function setPriceForAllDomains(ProductData $productData, $price)
    {
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);

        $manualInputPrices = [];
        foreach ($pricingGroupFacade->getAll() as $pricingGroup) {
            $manualInputPrices[$pricingGroup->getId()] = $price;
        }

        $productData->manualInputPricesByPricingGroupId = $manualInputPrices;
    }

    public function testIsVisibleOnAnyDomainWhenHidden()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $productData->hidden = true;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\ShopBundle\Model\Product\Product $productAgain */
        $productAgain = $em->getRepository(Product::class)->find($id);

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility1 */
        $productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $productAgain,
            'pricingGroup' => $pricingGroup->getId(),
            'domainId' => 1,
        ]);

        $this->assertFalse($productAgain->isVisible());
        $this->assertFalse($productVisibility1->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenNotHidden()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\ShopBundle\Model\Product\Product $productAgain */
        $productAgain = $em->getRepository(Product::class)->find($id);

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility1 */
        $productVisibility1 = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $productAgain->getId(),
            'pricingGroup' => $pricingGroup->getId(),
            'domainId' => 1,
        ]);

        $this->assertTrue($productAgain->isVisible());
        $this->assertTrue($productVisibility1->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingInFuture()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $sellingFrom = new DateTime('now');
        $sellingFrom->modify('+1 day');

        $productData = $this->getDefaultProductData();
        $productData->sellingFrom = $sellingFrom;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\ShopBundle\Model\Product\Product $productAgain */
        $productAgain = $em->getRepository(Product::class)->find($id);

        $this->assertFalse($productAgain->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingInPast()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $sellingTo = new DateTime('now');
        $sellingTo->modify('-1 day');

        $productData = $this->getDefaultProductData();
        $productData->sellingTo = $sellingTo;
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->flush();
        $id = $product->getId();
        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\ShopBundle\Model\Product\Product $productAgain */
        $productAgain = $em->getRepository(Product::class)->find($id);

        $this->assertFalse($productAgain->isVisible());
    }

    public function testIsVisibleOnAnyDomainWhenSellingNow()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

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

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\ShopBundle\Model\Product\Product $productAgain */
        $productAgain = $em->getRepository(Product::class)->find($id);

        $this->assertTrue($productAgain->isVisible());
    }

    public function testIsNotVisibleWhenZeroOrNullPrice()
    {
        $em = $this->getEntityManager();
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $this->setPriceForAllDomains($productData, 0);
        $product1 = $productFacade->create($productData);

        $this->setPriceForAllDomains($productData, null);
        $product2 = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $product1Id = $product1->getId();
        $product2Id = $product2->getId();
        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\ShopBundle\Model\Product\Product $product1Again */
        $product1Again = $em->getRepository(Product::class)->find($product1Id);
        /** @var \Shopsys\ShopBundle\Model\Product\Product $product2Again */
        $product2Again = $em->getRepository(Product::class)->find($product2Id);

        $this->assertFalse($product1Again->isVisible());
        $this->assertFalse($product2Again->isVisible());
    }

    public function testIsVisibleWithFilledName()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $productData->name = ['cs' => 'Name', 'en' => 'Name'];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility */
        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup->getId(),
            'domainId' => 1,
        ]);

        $this->assertTrue($productVisibility->isVisible());
    }

    public function testIsNotVisibleWithEmptyName()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $productData->name = ['cs' => null, 'en' => null];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility */
        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup->getId(),
            'domainId' => 1,
        ]);

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsVisibleInVisibileCategory()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $category = $this->getReference(CategoryDataFixture::CATEGORY_TOYS);

        $productData = $this->getDefaultProductData();
        $productData->categoriesByDomainId = [1 => [$category]];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility */
        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup->getId(),
            'domainId' => 1,
        ]);

        $this->assertTrue($productVisibility->isVisible());
    }

    public function testIsNotVisibleInHiddenCategory()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $productData->categoriesByDomainId = [];
        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup->getId(),
            'domainId' => 1,
        ]);

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsNotVisibleWhenZeroManualPrice()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);

        $productData = $this->getDefaultProductData();
        $this->setPriceForAllDomains($productData, 10);

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        $pricingGroupWithZeroPriceId = $pricingGroup->getId();

        $productData->manualInputPricesByPricingGroupId[$pricingGroupWithZeroPriceId] = 0;

        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility */
        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroupWithZeroPriceId,
            'domainId' => 1,
        ]);

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testIsNotVisibleWhenNullManualPrice()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);

        $productData = $this->getDefaultProductData();

        $allPricingGroups = $pricingGroupFacade->getAll();
        foreach ($allPricingGroups as $pricingGroup) {
            $productData->manualInputPricesByPricingGroupId[$pricingGroup->getId()] = 10;
        }

        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        $pricingGroupWithNullPriceId = $pricingGroup->getId();
        $productData->manualInputPricesByPricingGroupId[$pricingGroupWithNullPriceId] = null;

        $product = $productFacade->create($productData);
        $productPriceRecalculator->runImmediateRecalculations();

        $em->clear();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        $productVisibilityRepository->refreshProductsVisibility();

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibility $productVisibility */
        $productVisibility = $em->getRepository(ProductVisibility::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroupWithNullPriceId,
            'domainId' => 1,
        ]);

        $this->assertFalse($productVisibility->isVisible());
    }

    public function testRefreshProductsVisibilityVisibleVariants()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant1 */
        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant2 */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant3 */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $mainVariant */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');

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
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant1 */
        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant2 */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant3 */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $mainVariant */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');

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
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityRepository $productVisibilityRepository */
        $productVisibilityRepository = $this->getContainer()->get(ProductVisibilityRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant1 */
        $variant1 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '53');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant2 */
        $variant2 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '54');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $variant3 */
        $variant3 = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '69');
        /** @var \Shopsys\ShopBundle\Model\Product\Product $mainVariant */
        $mainVariant = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '148');

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
