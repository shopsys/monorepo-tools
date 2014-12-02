<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;

class PricingGroupDataFixture extends AbstractReferenceFixture {

	const ORDINARY_FIRST = 'ordinary_first';
	const ORDINARY_SECOND = 'ordinary_second';
	const VIP_FIRST = 'vip_first';
	const VIP_SECOND = 'vip_second';

	public function load(ObjectManager $manager) {
		$pricingGroupData = new PricingGroupData();

		$pricingGroupData->setName('Obyčejný zákazník');
		$this->createPricingGroup($manager, $pricingGroupData, 1, self::ORDINARY_FIRST);

		$pricingGroupData->setName('VIP zákazník');
		$this->createPricingGroup($manager, $pricingGroupData, 1, self::VIP_FIRST);

		$pricingGroupData->setName('Ordinary customer');
		$this->createPricingGroup($manager, $pricingGroupData, 2, self::ORDINARY_SECOND);

		$pricingGroupData->setName('VIP customer');
		$this->createPricingGroup($manager, $pricingGroupData, 2, self::VIP_SECOND);

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
		$domainId,
		$referenceName
	) {
		$pricingGroup = new PricingGroup($pricingGroupData, $domainId);
		$manager->persist($pricingGroup);
		$this->addReference($referenceName, $pricingGroup);
	}
}
