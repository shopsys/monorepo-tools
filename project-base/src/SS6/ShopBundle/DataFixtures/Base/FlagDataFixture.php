<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Product\Flag\Flag;
use SS6\ShopBundle\Model\Product\Flag\FlagData;

class FlagDataFixture extends AbstractReferenceFixture {

	const NEW_PRODUCT = 'flag_new_product';
	const TOP_PRODUCT = 'flag_top_product';
	const ACTION_PRODUCT = 'flag_action';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$flagData = new FlagData();

		$flagData->name = ['cs' => 'Novinka', 'en' => 'New'];
		$flagData->rgbColor = '#EFD6FF';
		$this->createFlag($manager, self::NEW_PRODUCT, $flagData);

		$flagData->name = ['cs' => 'Nejprodávanější', 'en' => 'TOP'];
		$flagData->rgbColor = '#D6FFFA';
		$this->createFlag($manager, self::TOP_PRODUCT, $flagData);

		$flagData->name = ['cs' => 'Akce', 'en' => 'Action'];
		$flagData->rgbColor = '#F9FFD6';
		$this->createFlag($manager, self::ACTION_PRODUCT, $flagData);

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Product\Flag\FlagData $flagData
	 */
	private function createFlag(ObjectManager $manager, $referenceName, FlagData $flagData) {
		$flag = new Flag($flagData);
		$manager->persist($flag);
		$this->addReference($referenceName, $flag);
	}

}
