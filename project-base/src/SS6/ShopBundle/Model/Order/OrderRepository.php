<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;

class OrderRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
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
	 * @param int $userId
	 * @return \SS6\ShopBundle\Model\Order\Order|null
	 */
	public function findLastByUserId($userId) {
		return $this->getOrderRepository()->findOneBy(
			array(
				'customer' => $userId,
			),
			array(
				'createdAt' => 'DESC'
			)
		);
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
	 */
	public function getById($id) {
		$order = $this->findById($id);

		if ($order === null) {
			throw new \SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException($id);
		}

		return $order;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return int
	 */
	public function getOrdersCountByStatus(OrderStatus $orderStatus) {
		$query = $this->em->createQuery('
			SELECT COUNT(o)
			FROM ' . Order::class . ' o
			WHERE o.status = :status')
			->setParameter('status', $orderStatus->getId());
		$result = $query->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
		return $result;
	}

	/**
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getOrdersListQueryBuilder() {
		return $this->em->createQueryBuilder()
			->select('o')
			->from(Order::class, 'o')
			->where('o.deleted = :deleted')
			->setParameter('deleted', false);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User
	 * @return \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function getCustomerOrderList(User $user) {
		return $this->em->createQueryBuilder()
			->select('o, oi, os, ost, c')
			->from(Order::class, 'o')
			->join('o.items', 'oi')
			->join('o.status', 'os')
			->join('os.translations', 'ost')
			->join('o.currency', 'c')
			->where('o.customer = :customer AND o.deleted = :deleted')
			->orderBy('o.createdAt', 'DESC')
			->setParameter('customer', $user)
			->setParameter('deleted', false)
			->getQuery()->execute();
	}

	/*
	 * @param int $orderStatusId
	 * @return \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function findByStatusId($orderStatusId) {
		return $this->getOrderRepository()->findBy(array(
			'status' => $orderStatusId,
		));
	}

	/**
	 * @param string $urlHash
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getByUrlHash($urlHash) {
		$order = $this->getOrderRepository()->findOneBy(['urlHash' => $urlHash]);

		if ($order === null) {
			throw new \SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException($urlHash);
		}

		return $order;
	}

	/**
	 * @param string $orderNumber
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getByOrderNumberAndUser($orderNumber, User $user) {
		$criteria = ['number' => $orderNumber, 'customer' => $user];
		$order = $this->getOrderRepository()->findOneBy($criteria);

		if ($order === null) {
			throw new \SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException($criteria);
		}

		return $order;
	}

	/**
	 * @param string $urlHash
	 * @return \SS6\ShopBundle\Model\Order\Order|null
	 */
	public function findByUrlHash($urlHash) {
		return $this->getOrderRepository()->findOneBy(['urlHash' => $urlHash]);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Currency\Currency[]
	 */
	public function getCurrenciesUsedInOrders() {
		return $this->em->createQueryBuilder()
			->select('c')
			->from(Currency::class, 'c')
			->join(Order::class, 'o', Join::WITH, 'o.currency = c.id')
			->groupBy('c')
			->getQuery()->execute();
	}
}
