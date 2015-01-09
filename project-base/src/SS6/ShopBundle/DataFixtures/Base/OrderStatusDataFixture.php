<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;

class OrderStatusDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$orderStatusData = new OrderStatusData();
		$orderStatusData->name = ['cs' => 'Nová', 'en' => 'New'];
		$this->createOrderStatus($manager, 'order_status_new', $orderStatusData, OrderStatus::TYPE_NEW);

		$orderStatusData->name = ['cs' => 'Vyřizuje se', 'en' => 'In progress'];
		$this->createOrderStatus($manager, 'order_status_in_progress', $orderStatusData, OrderStatus::TYPE_IN_PROGRESS);

		$orderStatusData->name = ['cs' => 'Vyřízena', 'en' => 'Done'];
		$this->createOrderStatus($manager, 'order_status_done', $orderStatusData, OrderStatus::TYPE_DONE);

		$orderStatusData->name =['cs' => 'Stornována', 'en' => 'Canceled'];
		$this->createOrderStatus($manager, 'order_status_canceled', $orderStatusData, OrderStatus::TYPE_CANCELED);

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
	 * @param int $type
	 */
	public function createOrderStatus(ObjectManager $manager, $referenceName, OrderStatusData $orderStatusData, $type) {
		$orderStatus = new OrderStatus($orderStatusData, $type);
		$manager->persist($orderStatus);
		$this->addReference($referenceName, $orderStatus);
	}

}
