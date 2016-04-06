<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;

class OrderStatusDataFixture extends AbstractReferenceFixture {

	const ORDER_STATUS_NEW = 'order_status_new';
	const ORDER_STATUS_IN_PROGRESS = 'order_status_in_progress';
	const ORDER_STATUS_DONE = 'order_status_done';
	const ORDER_STATUS_CANCELED = 'order_status_canceled';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$orderStatusData = new OrderStatusData();
		$orderStatusData->name = ['cs' => 'Nová', 'en' => 'New'];
		$this->createOrderStatus($manager, self::ORDER_STATUS_NEW, $orderStatusData, OrderStatus::TYPE_NEW);

		$orderStatusData->name = ['cs' => 'Vyřizuje se', 'en' => 'In progress'];
		$this->createOrderStatus($manager, self::ORDER_STATUS_IN_PROGRESS, $orderStatusData, OrderStatus::TYPE_IN_PROGRESS);

		$orderStatusData->name = ['cs' => 'Vyřízena', 'en' => 'Done'];
		$this->createOrderStatus($manager, self::ORDER_STATUS_DONE, $orderStatusData, OrderStatus::TYPE_DONE);

		$orderStatusData->name = ['cs' => 'Stornována', 'en' => 'Canceled'];
		$this->createOrderStatus($manager, self::ORDER_STATUS_CANCELED, $orderStatusData, OrderStatus::TYPE_CANCELED);
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
	 * @param int $type
	 */
	private function createOrderStatus(ObjectManager $manager, $referenceName, OrderStatusData $orderStatusData, $type) {
		$orderStatus = new OrderStatus($orderStatusData, $type);
		$manager->persist($orderStatus);
		$manager->flush($orderStatus);
		$this->addReference($referenceName, $orderStatus);
	}

}
