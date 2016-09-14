<?php

namespace SS6\ShopBundle\Tests\Database\Model\Pricing\Group;

use ReflectionClass;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerFacade;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class PricingGroupFacadeTest extends DatabaseTestCase {

	public function testCreate() {
		$em = $this->getEntityManager();
		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $prodcu \SS6\ShopBundle\Model\Product\Product */
		$pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$pricingGroupData = new PricingGroupData('pricing_group_name', 1);
		$domainId = 1;
		$pricingGroup = $pricingGroupFacade->create($pricingGroupData, $domainId);
		$productPriceRecalculator->runAllScheduledRecalculations();
		$productCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
			'product' => $product,
			'pricingGroup' => $pricingGroup,
		]);

		$this->assertNotNull($productCalculatedPrice);
	}

	public function testEdit() {
		$em = $this->getEntityManager();
		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $prodcu \SS6\ShopBundle\Model\Product\Product */
		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */
		$pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$productCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
			'product' => $product,
			'pricingGroup' => $pricingGroup,
		]);

		$reflectionClass = new ReflectionClass(ProductCalculatedPrice::class);
		$reflectionProperty = $reflectionClass->getProperty('priceWithVat');
		$reflectionProperty->setAccessible(true);

		$productPriceBeforeEdit = $reflectionProperty->getValue($productCalculatedPrice);

		$pricingGroupData = new PricingGroupData($pricingGroup->getName(), $pricingGroup->getCoefficient() * 2);
		$pricingGroupFacade->edit($pricingGroup->getId(), $pricingGroupData);
		$productPriceRecalculator->runAllScheduledRecalculations();

		$newProductCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
			'product' => $product,
			'pricingGroup' => $pricingGroup,
		]);

		$productPriceAfterEdit = $reflectionProperty->getValue($newProductCalculatedPrice);

		$this->assertSame(round($productPriceBeforeEdit * 2, 6), round($productPriceAfterEdit, 6));
	}

	public function testDeleteAndReplace() {
		$em = $this->getEntityManager();
		$pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */
		$customerFacade = $this->getContainer()->get(CustomerFacade::class);
		/* @var $customerFacade \SS6\ShopBundle\Model\Customer\CustomerFacade */

		$domainId = 1;
		$pricingGroupToDelete = $pricingGroupFacade->create(new PricingGroupData('name'), $domainId);
		$pricingGroupToReplaceWith = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */
		$user = $customerFacade->getUserById(1);
		/* @var $user \SS6\ShopBundle\Model\Customer\User */
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
