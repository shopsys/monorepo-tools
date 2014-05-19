<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\Order;

class OrderRepository {
	
	/** 
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @return \Doctrine\ORM\EntityRepository
	 */
	private function getOrderRepository() {
		return $this->em->getRepository(Order::class);
	}

	/**
	 * @param int $userId
	 * @return \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function findByUserId($userId) {
		return $this->getOrderRepository()->findBy(array(
			'customer' => $userId,
		));
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Order\Order|null
	 */
	public function findById($id) {
		return $this->getOrderRepository()->find($id);
	}
	
	/**
	 * @param int $id
	 * @return \SS6\ShopBundle\Model\Order\Order
	 * @throws \SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException
	 */
	public function getById($id) {
		$order = $this->findById($id);

		if ($order === null) {
			throw new \SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException($id);
		}

		return $order;
	}
	
}
