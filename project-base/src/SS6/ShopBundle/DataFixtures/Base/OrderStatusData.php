<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderStatusData extends AbstractFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->createOrderStatus($manager, 'order_status_new', 'Nová', OrderStatusRepository::STATUS_NEW);
		$this->createOrderStatus($manager, 'order_status_in_progress', 'Rozpracovaná', OrderStatusRepository::STATUS_IN_PROGRESS);
		$this->createOrderStatus($manager, 'order_status_done', 'Vyřízená', OrderStatusRepository::STATUS_DONE);
		$manager->flush();
	}

	public function createOrderStatus(ObjectManager $manager, $referenceName, $name, $orderStatusId) {
		$orderStatus = new OrderStatus($name, $orderStatusId);
		$manager->persist($orderStatus);
		$this->addReference($referenceName, $orderStatus);
	}

}
