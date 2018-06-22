<?php

namespace Tests\ShopBundle\Database\Model\Pricing\Group;

use ReflectionClass;
use Shopsys\FrameworkBundle\DataFixtures\Base\PricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\Model\Customer\CustomerData;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Tests\ShopBundle\Test\DatabaseTestCase;

class PricingGroupFacadeTest extends DatabaseTestCase
{
    public function testCreate()
    {
        $em = $this->getEntityManager();
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $prodcu \Shopsys\FrameworkBundle\Model\Product\Product */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */
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
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /* @var $prodcu \Shopsys\FrameworkBundle\Model\Product\Product */
        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */
        $productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
        /* @var $productPriceRecalculator \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculator */
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
        $pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
        /* @var $pricingGroupFacade \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupFacade */
        $customerFacade = $this->getContainer()->get(CustomerFacade::class);
        /* @var $customerFacade \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade */

        $domainId = 1;
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupToDelete = $pricingGroupFacade->create($pricingGroupData, $domainId);
        $pricingGroupToReplaceWith = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        /* @var $pricingGroup \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup */
        $user = $customerFacade->getUserById(1);
        /* @var $user \Shopsys\FrameworkBundle\Model\Customer\User */
        $userData = new UserData();
        $userData->setFromEntity($user);

        $userData->pricingGroup = $pricingGroupToDelete;
        $customerData = new CustomerData($userData);
        $customerFacade->editByAdmin($user->getId(), $customerData);

        $pricingGroupFacade->delete($pricingGroupToDelete->getId(), $pricingGroupToReplaceWith->getId());

        $em->refresh($user);

        $this->assertEquals($pricingGroupToReplaceWith, $user->getPricingGroup());
    }
}
