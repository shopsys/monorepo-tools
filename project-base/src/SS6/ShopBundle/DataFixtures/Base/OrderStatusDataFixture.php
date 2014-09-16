<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderStatusDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		// @codingStandardsIgnoreStart
		$this->createOrderStatus($manager, 'order_status_new', 'Nová', OrderStatus::TYPE_NEW, OrderStatusRepository::STATUS_NEW);
		$this->createOrderStatus($manager, 'order_status_in_progress', 'Vyřizuje se', OrderStatus::TYPE_IN_PROGRESS);
		$this->createOrderStatus($manager, 'order_status_done', 'Vyřízena', OrderStatus::TYPE_DONE);
		$this->createOrderStatus($manager, 'order_status_canceled', 'Stornována', OrderStatus::TYPE_CANCELED);
		// @codingStandardsIgnoreStop

		$manager->flush();
	}

	public function createOrderStatus(ObjectManager $manager, $referenceName, $name, $type, $orderStatusId = null) {
		$orderStatus = new OrderStatus($name, $type, $orderStatusId);
		$manager->persist($orderStatus);
		$this->addReference($referenceName, $orderStatus);
	}

}
