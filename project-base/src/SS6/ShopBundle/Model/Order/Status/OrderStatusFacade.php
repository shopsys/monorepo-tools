<?php

namespace SS6\ShopBundle\Model\Order\Status;

use Doctrine\ORM\EntityManager;
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
		
	public function __construct(EntityManager $em, OrderStatusRepository $orderStatusRepository, OrderStatusService $orderStatusService) {
		$this->em = $em;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderStatusService = $orderStatusService;
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
