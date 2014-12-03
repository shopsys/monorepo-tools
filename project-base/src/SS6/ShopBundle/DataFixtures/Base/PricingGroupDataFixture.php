<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;

class PricingGroupDataFixture extends AbstractReferenceFixture {

	const ORDINARY_DOMAIN_1 = 'ordinary_domain_1';
	const ORDINARY_DOMAIN_2 = 'ordinary_domain_2';
	const VIP_DOMAIN_1 = 'vip_domain_1';
	const VIP_DOMAIN_2 = 'vip_domain_2';

	public function load(ObjectManager $manager) {
		$pricingGroupData = new PricingGroupData();

		$pricingGroupData->setName('Obyčejný zákazník');
		$this->createPricingGroup($manager, $pricingGroupData, 1, self::ORDINARY_DOMAIN_1);

		$pricingGroupData->setName('VIP zákazník');
		$this->createPricingGroup($manager, $pricingGroupData, 1, self::VIP_DOMAIN_1);

		$pricingGroupData->setName('Ordinary customer');
		$this->createPricingGroup($manager, $pricingGroupData, 2, self::ORDINARY_DOMAIN_2);

		$pricingGroupData->setName('VIP customer');
		$this->createPricingGroup($manager, $pricingGroupData, 2, self::VIP_DOMAIN_2);

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
