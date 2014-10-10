<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Order\OrderRepository;
use SS6\ShopBundle\Model\Order\OrderService;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use SS6\ShopBundle\Model\Order\Status\OrderStatusService;

class OrderStatusFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusService
	 */
	private $orderStatusService;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderService
	 */
	private $orderService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusService $orderStatusService
	 * @param \SS6\ShopBundle\Model\Order\OrderRepository $orderRepository
	 */
	public function __construct(EntityManager $em, OrderStatusRepository $orderStatusRepository,
		OrderStatusService $orderStatusService, OrderRepository $orderRepository,
		OrderService $orderService
	) {
		$this->em = $em;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderStatusService = $orderStatusService;
		$this->orderRepository = $orderRepository;
		$this->orderService = $orderService;
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusFormData
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function create(OrderStatusData $orderStatusFormData) {
		$orderStatus = $this->orderStatusService->create(
			$orderStatusFormData->getName(),
			OrderStatus::TYPE_IN_PROGRESS
		);
		$this->em->persist($orderStatus);
		$this->em->flush();

		return $orderStatus;
	}

	/**
	 * @param int $orderStatusId
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusFormData
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function edit($orderStatusId, OrderStatusData $orderStatusFormData) {
		$orderStatus = $this->orderStatusRepository->getById($orderStatusId);
		$this->orderStatusService->edit($orderStatus, $orderStatusFormData->getName());
		$this->em->flush();

		return $orderStatus;
	}

	/**
	 * @param int $orderStatusId
	 * @param int|null $newOrderStatusId
	 */
	public function deleteById($orderStatusId, $newOrderStatusId = null) {
		$orderStatus = $this->orderStatusRepository->getById($orderStatusId);
		$orders = $this->orderRepository->findByStatusId($orderStatusId);
		if ($newOrderStatusId !== null) {
			$newOrderStatus = $this->orderStatusRepository->findById($newOrderStatusId);
		} else {
			$newOrderStatus = null;
		}
		
		$this->orderStatusService->delete($orderStatus, $orders, $newOrderStatus);

		$this->em->remove($orderStatus);
		$this->em->flush();
	}

	/**
	 * @param int $orderStatusId
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function getById($orderStatusId) {
		return $this->orderStatusRepository->getById($orderStatusId);
	}

	/**
	 * @param int $orderStatusId
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	public function getAllExceptId($orderStatusId) {
		return $this->orderStatusRepository->getAllExceptId($orderStatusId);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return bool
	 */
	public function isOrderStatusUsed(OrderStatus $orderStatus) {
		return $this->orderRepository->getOrdersCountByStatus($orderStatus);
	}

}
