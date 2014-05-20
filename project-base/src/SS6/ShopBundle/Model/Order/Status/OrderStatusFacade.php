<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormData;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusService $orderStatusService
	 */
	public function __construct(EntityManager $em, OrderStatusRepository $orderStatusRepository, 
		OrderStatusService $orderStatusService
	) {
		$this->em = $em;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderStatusService = $orderStatusService;
	}

	/**
	 *
	 * @param \SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormData $orderStatusFormData
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function create(OrderStatusFormData $orderStatusFormData) {
		$orderStatus = $this->orderStatusService->create($orderStatusFormData->getName());
		$this->em->persist($orderStatus);
		$this->em->flush();

		return $orderStatus;
	}

	/**
	 * @param int $orderStatusId
	 * @param \SS6\ShopBundle\Form\Admin\Order\Status\OrderStatusFormData $orderStatusFormData
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus
	 */
	public function edit($orderStatusId, OrderStatusFormData $orderStatusFormData) {
		$orderStatus = $this->orderStatusRepository->getById($orderStatusId);
		$this->orderStatusService->edit($orderStatus, $orderStatusFormData->getName());
		$this->em->flush();

		return $orderStatus;
	}

	/**
	 * @param int $orderStatusId
	 */
	public function deleteById($orderStatusId) {
		$orderStatus = $this->orderStatusRepository->getById($orderStatusId);
		$this->orderStatusService->delete($orderStatus);
		$this->em->remove($orderStatus);
		$this->em->flush();
	}
}
