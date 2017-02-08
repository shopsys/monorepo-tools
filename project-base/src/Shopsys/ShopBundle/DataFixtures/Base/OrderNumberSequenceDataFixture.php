<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\OrderNumberSequence;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;

class OrderNumberSequenceDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$orderNumberSequence = new OrderNumberSequence(OrderNumberSequenceRepository::ID, 0);
		$manager->persist($orderNumberSequence);
		$manager->flush();
	}

}
