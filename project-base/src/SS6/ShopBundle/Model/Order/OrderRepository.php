<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;

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
	 * @throws \SS6\ShopBundle\Model\Order\Exception\OrderNotFoundException
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
	 * @return array
	 */
	public function getCustomerOrderListData(User $user) {
		return $this->em->createQueryBuilder()
			->select('
				o.number,
				o.createdAt,
				MAX(os.name) AS statusName,
				COUNT(oiProduct.id) AS itemsCount,
				MAX(oiTransport.name) AS transportName,
				MAX(oiPayment.name) AS paymentName,
				o.totalPriceWithVat
				')
			->from(Order::class, 'o')
			->leftJoin('o.items', 'oiProduct', Join::WITH, 'oiProduct INSTANCE OF :typeProduct')
			->leftJoin('o.items', 'oiTransport', Join::WITH, 'oiTransport INSTANCE OF :typeTransport')
			->leftJoin('o.items', 'oiPayment', Join::WITH, 'oiPayment INSTANCE OF :typePayment')
			->join('o.status', 'os')
			->groupBy('o.id')
			->where('o.customer = :customer AND o.deleted = :deleted')
			->setParameter('customer', $user)
			->setParameter('deleted', false)
			->setParameter('typePayment', 'payment')
			->setParameter('typeProduct', 'product')
			->setParameter('typeTransport', 'transport')
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

}
