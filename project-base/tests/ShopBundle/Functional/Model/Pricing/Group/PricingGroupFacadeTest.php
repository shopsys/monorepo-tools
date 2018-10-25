<?php

namespace Tests\ShopBundle\Functional\Model\Pricing\Group;

use ReflectionClass;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactory;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\UserDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class PricingGroupFacadeTest extends TransactionFunctionalTestCase
{
    public function testCreate()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\ShopBundle\Model\Product\Product $product */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'pricing_group_name';
        $pricingGroupData->coefficient = 1;
        $domainId = 1;
        $pricingGroup = $pricingGroupFacade->create($pricingGroupData, $domainId);
        $productPriceRecalculator->runAllScheduledRecalculations();
        $productCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup,
        ]);

        $this->assertNotNull($productCalculatedPrice);
    }

    public function testEdit()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\ShopBundle\Model\Product\Product $product */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator $productPriceRecalculator */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        $productCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup,
        ]);

        $reflectionClass = new ReflectionClass(ProductCalculatedPrice::class);
        $reflectionProperty = $reflectionClass->getProperty('priceWithVat');
        $reflectionProperty->setAccessible(true);

        $productPriceBeforeEdit = $reflectionProperty->getValue($productCalculatedPrice);

        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = $pricingGroup->getName();
        $pricingGroupData->coefficient = $pricingGroup->getCoefficient() * 2;
        $pricingGroupFacade->edit($pricingGroup->getId(), $pricingGroupData);
        $productPriceRecalculator->runAllScheduledRecalculations();

        $newProductCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup,
        ]);

        $productPriceAfterEdit = $reflectionProperty->getValue($newProductCalculatedPrice);

        $this->assertSame(round($productPriceBeforeEdit * 2, 6), round($productPriceAfterEdit, 6));
    }

    public function testDeleteAndReplace()
    {
        $em = $this->getEntityManager();
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade $pricingGroupFacade */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade */
        $customerFacade = $this->getContainer()->get(CustomerFacade::class);

        $domainId = 1;
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupToDelete = $pricingGroupFacade->create($pricingGroupData, $domainId);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroupToReplaceWith */
        $pricingGroupToReplaceWith = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /** @var \Shopsys\ShopBundle\Model\Customer\User $user */
        $user = $customerFacade->getUserById(1);
        /** @var \Shopsys\ShopBundle\Model\Customer\UserDataFactory $userDataFactory */
        $userDataFactory = $this->getContainer()->get(UserDataFactoryInterface::class);
        $userData = $userDataFactory->createFromUser($user);
        /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactory $customerDataFactory */
        $customerDataFactory = $this->getContainer()->get(CustomerDataFactory::class);

        $userData->pricingGroup = $pricingGroupToDelete;
        $customerData = $customerDataFactory->create();
        $customerData->userData = $userData;
        $customerFacade->editByAdmin($user->getId(), $customerData);

        $pricingGroupFacade->delete($pricingGroupToDelete->getId(), $pricingGroupToReplaceWith->getId());

        $em->refresh($user);

        $this->assertEquals($pricingGroupToReplaceWith, $user->getPricingGroup());
    }
}
