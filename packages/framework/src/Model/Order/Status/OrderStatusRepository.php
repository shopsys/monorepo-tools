<?php

namespace Shopsys\FrameworkBundle\Model\Order\Status;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Model\Order\Order;

class OrderStatusRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getOrderStatusRepository()
    {
        return $this->em->getRepository(OrderStatus::class);
    }

    /**
     * @param int $orderStatusId
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus|null
     */
    public function findById($orderStatusId)
    {
        return $this->getOrderStatusRepository()->find($orderStatusId);
    }

    /**
     * @param int $orderStatusId
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     */
    public function getById($orderStatusId)
    {
        $orderStatus = $this->findById($orderStatusId);

        if ($orderStatus === null) {
            $message = 'Order status with ID ' . $orderStatusId . ' not found.';
            throw new \Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusNotFoundException($message);
        }

        return $orderStatus;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus
     */
    public function getDefault()
    {
        $orderStatus = $this->getOrderStatusRepository()->findOneBy(['type' => OrderStatus::TYPE_NEW]);

        if ($orderStatus === null) {
            $message = 'Default order status not found.';
            throw new \Shopsys\FrameworkBundle\Model\Order\Status\Exception\OrderStatusNotFoundException($message);
        }

        return $orderStatus;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus[]
     */
    public function getAll()
    {
        return $this->getOrderStatusRepository()->findBy([], ['id' => 'asc']);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus[]
     */
    public function getAllIndexedById()
    {
        $orderStatusesIndexedById = [];

        foreach ($this->getAll() as $orderStatus) {
            $orderStatusesIndexedById[$orderStatus->getId()] = $orderStatus;
        }

        return $orderStatusesIndexedById;
    }

    /**
     * @param int $orderStatusId
     * @return \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus[]
     */
    public function getAllExceptId($orderStatusId)
    {
        $qb = $this->getOrderStatusRepository()->createQueryBuilder('os')
            ->where('os.id != :id')
            ->setParameter('id', $orderStatusId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $oldOrderStatus
     * @param \Shopsys\FrameworkBundle\Model\Order\Status\OrderStatus $newOrderStatus
     */
    public function replaceOrderStatus(OrderStatus $oldOrderStatus, OrderStatus $newOrderStatus)
    {
        $this->em->createQueryBuilder()
            ->update(Order::class, 'o')
            ->set('o.status', ':newOrderStatus')->setParameter('newOrderStatus', $newOrderStatus)
            ->where('o.status = :oldOrderStatus')->setParameter('oldOrderStatus', $oldOrderStatus)
            ->getQuery()->execute();
    }
}
