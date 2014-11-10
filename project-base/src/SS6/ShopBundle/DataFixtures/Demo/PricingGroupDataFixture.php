<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;

class PricingGroupDataFixture extends AbstractReferenceFixture {

	public function load(ObjectManager $manager) {
		$pricingGroupData = new PricingGroupData();

		$pricingGroupData->setName('Obyčejný zákazník');
		$this->createPricingGroup($manager, $pricingGroupData, 1);

		$pricingGroupData->setName('VIP zákazník');
		$this->createPricingGroup($manager, $pricingGroupData, 1);

		$pricingGroupData->setName('Ordinary customer');
		$this->createPricingGroup($manager, $pricingGroupData, 2);

		$pricingGroupData->setName('VIP customer');
		$this->createPricingGroup($manager, $pricingGroupData, 2);

		$manager->flush();

	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroupData $pricingGroupData
	 * @param int $domainId
	 */
	private function createPricingGroup(
		ObjectManager $manager,
		PricingGroupData $pricingGroupData,
		$domainId
	) {
		$pricingGroup = new PricingGroup($pricingGroupData, $domainId);
		$manager->persist($pricingGroup);
	}
}
