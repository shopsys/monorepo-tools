<?php

namespace Shopsys\ShopBundle\Model\Order\Status;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Mail\MailTemplateFacade;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailService;
use Shopsys\ShopBundle\Model\Order\OrderRepository;
use Shopsys\ShopBundle\Model\Order\OrderService;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusData;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository;
use Shopsys\ShopBundle\Model\Order\Status\OrderStatusService;

class OrderStatusFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusRepository
     */
    private $orderStatusRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Status\OrderStatusService
     */
    private $orderStatusService;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderService
     */
    private $orderService;

    /**
     * @var \Shopsys\ShopBundle\Model\Mail\MailTemplateFacade
     */
    private $mailTemplateFacade;

    public function __construct(
        EntityManager $em,
        OrderStatusRepository $orderStatusRepository,
        OrderStatusService $orderStatusService,
        OrderRepository $orderRepository,
        OrderService $orderService,
        MailTemplateFacade $mailTemplateFacade
    ) {
        $this->em = $em;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStatusService = $orderStatusService;
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
        $this->mailTemplateFacade = $mailTemplateFacade;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusFormData
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus
     */
    public function create(OrderStatusData $orderStatusFormData) {
        $orderStatus = new OrderStatus($orderStatusFormData, OrderStatus::TYPE_IN_PROGRESS);
        $this->em->persist($orderStatus);
        $this->em->flush();

        $this->mailTemplateFacade->createMailTemplateForAllDomains(
            OrderMailService::MAIL_TEMPLATE_NAME_PREFIX . $orderStatus->getId()
        );

        return $orderStatus;
    }

    /**
     * @param int $orderStatusId
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatusData $orderStatusData
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus
     */
    public function edit($orderStatusId, OrderStatusData $orderStatusData) {
        $orderStatus = $this->orderStatusRepository->getById($orderStatusId);
        $orderStatus->edit($orderStatusData);
        $this->em->flush();

        return $orderStatus;
    }

    /**
     * @param int $orderStatusId
     * @param int|null $newOrderStatusId
     */
    public function deleteById($orderStatusId, $newOrderStatusId = null) {
        $orderStatus = $this->orderStatusRepository->getById($orderStatusId);
        $this->orderStatusService->checkForDelete($orderStatus);
        if ($newOrderStatusId !== null) {
            $newOrderStatus = $this->orderStatusRepository->findById($newOrderStatusId);
            $this->orderStatusRepository->replaceOrderStatus($orderStatus, $newOrderStatus);
        }

        $this->em->remove($orderStatus);
        $this->em->flush();
    }

    /**
     * @param int $orderStatusId
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus
     */
    public function getById($orderStatusId) {
        return $this->orderStatusRepository->getById($orderStatusId);
    }

    /**
     * @param int $orderStatusId
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus[]
     */
    public function getAllExceptId($orderStatusId) {
        return $this->orderStatusRepository->getAllExceptId($orderStatusId);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
     * @return bool
     */
    public function isOrderStatusUsed(OrderStatus $orderStatus) {
        return $this->orderRepository->isOrderStatusUsed($orderStatus);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus[]
     */
    public function getAll() {
        return $this->orderStatusRepository->getAll();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Status\OrderStatus[]
     */
    public function getAllIndexedById() {
        return $this->orderStatusRepository->getAllIndexedById();
    }
}
