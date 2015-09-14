<?php

namespace SS6\ShopBundle\Tests\Database\Model\Pricing\Group;

use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

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
		$pricingGroup = $pricingGroupFacade->create($pricingGroupData);
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
		$pricingGroup = $this->getReference('pricing_group_ordinary_domain_1');
		/* @var $pricingGroup \SS6\ShopBundle\Model\Pricing\Group\PricingGroup */
		$pricingGroupFacade = $this->getContainer()->get(PricingGroupFacade::class);
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */
		$productPriceRecalculator = $this->getContainer()->get(ProductPriceRecalculator::class);
		/* @var $productPriceRecalculator \SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator */
		$productCalculatedPrice = $em->getRepository(ProductCalculatedPrice::class)->findOneBy([
			'product' => $product,
			'pricingGroup' => $pricingGroup,
		]);
		/* @var $productCalculatedPrice \SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice */
		$productPriceBeforeEdit = $productCalculatedPrice->getPriceWithVat();

		$pricingGroupData = new PricingGroupData($pricingGroup->getName(), $pricingGroup->getCoefficient() * 2);
		$pricingGroupFacade->edit($pricingGroup->getId(), $pricingGroupData);
		$productPriceRecalculator->runAllScheduledRecalculations();

		$productPriceAfterEdit = $productCalculatedPrice->getPriceWithVat();

		$this->assertSame(round($productPriceBeforeEdit * 2, 6), round($productPriceAfterEdit, 6));
	}
}
