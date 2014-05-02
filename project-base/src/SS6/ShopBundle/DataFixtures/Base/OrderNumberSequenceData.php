<?php

namespace SS6\ShopBundle\DataFixtures\Base\Administrator;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Order\OrderNumberSequence;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;

class OrderNumberSequenceData extends AbstractFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$orderNumberSequence = new OrderNumberSequence(OrderNumberSequenceRepository::ID, 0);
		$manager->persist($orderNumberSequence);
		$manager->flush();
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}
