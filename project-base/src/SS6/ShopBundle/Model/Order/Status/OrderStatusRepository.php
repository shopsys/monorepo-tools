<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusRepository {

	const STATUS_NEW = 1;
	
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

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function getDefault() {
		return $this->getById(self::STATUS_NEW);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	public function findAll() {
		return $this->getOrderStatusRepository()->findBy(array(), array('id' => 'asc'));
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	public function getAllIndexedById() {
		$orderStatusesIndexedById = array();

		foreach ($this->findAll() as $orderStatus) {
			$orderStatusesIndexedById[$orderStatus->getId()] = $orderStatus;
		}

		return $orderStatusesIndexedById;
	}

	/**
	 * @param int $orderStatusId
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	public function findAllExceptId($orderStatusId) {
		$qb = $this->getOrderStatusRepository()->createQueryBuilder('os')
			->where('os.id != :id')
			->setParameter('id', $orderStatusId);

		return $qb->getQuery()->getResult();
	}

}
