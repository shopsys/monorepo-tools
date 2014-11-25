<?php

namespace SS6\ShopBundle\DataFixtures\Base;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class OrderStatusDataFixture extends AbstractReferenceFixture {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$names = array('cs' => 'Nová', 'en' => 'New');
		$this->createOrderStatus($manager, 'order_status_new', $names, OrderStatus::TYPE_NEW, OrderStatusRepository::STATUS_NEW);
		
		$names = array('cs' => 'Vyřizuje se', 'en' => 'In progress');
		$this->createOrderStatus($manager, 'order_status_in_progress', $names, OrderStatus::TYPE_IN_PROGRESS);

		$names = array('cs' => 'Vyřízena', 'en' => 'Done');
		$this->createOrderStatus($manager, 'order_status_done', $names, OrderStatus::TYPE_DONE);

		$names = array('cs' => 'Stornována', 'en' => 'Canceled');
		$this->createOrderStatus($manager, 'order_status_canceled', $names, OrderStatus::TYPE_CANCELED);

		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param string $referenceName
	 * @param array $names
	 * @param int $type
	 */
	public function createOrderStatus(ObjectManager $manager, $referenceName, array $names, $type) {
		$orderStatus = new OrderStatus($names, $type);
		$manager->persist($orderStatus);
		$this->addReference($referenceName, $orderStatus);
	}

}
