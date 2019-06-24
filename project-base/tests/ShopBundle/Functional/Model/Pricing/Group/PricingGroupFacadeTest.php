<?php

namespace Tests\ShopBundle\Functional\Model\Pricing\Group;

use Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\UserDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
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
        $domainId = 1;
        $pricingGroup = $pricingGroupFacade->create($pricingGroupData, $domainId);
        $productPriceRecalculator->runAllScheduledRecalculations();
        $productCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
            'product' => $product,
            'pricingGroup' => $pricingGroup,
        ]);

        $this->assertNotNull($productCalculatedPrice);
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
        $customerDataFactory = $this->getContainer()->get(CustomerDataFactoryInterface::class);

        $userData->pricingGroup = $pricingGroupToDelete;
        $customerData = $customerDataFactory->create();
        $customerData->userData = $userData;
        $customerFacade->editByAdmin($user->getId(), $customerData);

        $pricingGroupFacade->delete($pricingGroupToDelete->getId(), $pricingGroupToReplaceWith->getId());

        $em->refresh($user);

        $this->assertEquals($pricingGroupToReplaceWith, $user->getPricingGroup());
    }
}
