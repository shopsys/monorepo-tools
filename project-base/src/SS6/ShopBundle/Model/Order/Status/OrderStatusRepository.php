<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusRepository {
	
	/** 
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager) {
		$this->em = $entityManager;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getOrderStatusRepository() {
		return $this->em->getRepository(OrderStatus::class);
	}
	
	/**
	 * @param int $orderStatusId
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus|null
	 */
	public function findById($orderStatusId) {
		return $this->getOrderStatusRepository()->find($orderStatusId);
	}
	
	/**
	 * @param int $orderStatusId
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 * @throws \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException
	 */
	public function getById($orderStatusId) {
		$orderStatus = $this->findById($orderStatusId);

		if ($orderStatus === null) {
			throw new \SS6\ShopBundle\Model\Order\Status\Exception\OrderStatusNotFoundException($orderStatusId);
		}

		return $orderStatus;
	}
}
