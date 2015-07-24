<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserRepository;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Order\Mail\OrderMailFacade;
use SS6\ShopBundle\Model\Order\Mail\OrderMailService;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderCreationService;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderHashGeneratorRepository;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use SS6\ShopBundle\Model\Order\OrderService;
use SS6\ShopBundle\Model\Order\Preview\OrderPreview;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use SS6\ShopBundle\Model\Setting\Setting;

class OrderFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository
	 */
	private $orderNumberSequenceRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderRepository
	 */
	private $orderRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderService
	 */
	private $orderService;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation
	 */
	private $orderPreviewCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderCreationService
	 */
	private $orderCreationService;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserRepository
	 */
	private $userRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Mail\OrderMailFacade
	 */
	private $orderMailFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderHashGeneratorRepository
	 */
	private $orderHashGeneratorRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	public function __construct(
		EntityManager $em,
		OrderNumberSequenceRepository $orderNumberSequenceRepository,
		OrderRepository $orderRepository,
		OrderService $orderService,
		OrderPreviewCalculation $orderPreviewCalculation,
		OrderCreationService $orderCreationService,
		UserRepository $userRepository,
		OrderStatusRepository $orderStatusRepository,
		OrderMailFacade $orderMailFacade,
		OrderHashGeneratorRepository $orderHashGeneratorRepository,
		Setting $setting,
		Localization $localization
	) {
		$this->em = $em;
		$this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
		$this->orderRepository = $orderRepository;
		$this->orderService = $orderService;
		$this->orderPreviewCalculation = $orderPreviewCalculation;
		$this->orderCreationService = $orderCreationService;
		$this->userRepository = $userRepository;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderMailFacade = $orderMailFacade;
		$this->orderHashGeneratorRepository = $orderHashGeneratorRepository;
		$this->setting = $setting;
		$this->localization = $localization;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrder(OrderData $orderData, OrderPreview $orderPreview, User $user = null) {
		$orderStatus = $this->orderStatusRepository->getDefault();
		$orderNumber = $this->orderNumberSequenceRepository->getNextNumber();
		$orderUrlHash = $this->orderHashGeneratorRepository->getUniqueHash();

		$order = new Order(
			$orderData,
			$orderNumber,
			$orderStatus,
			$orderUrlHash,
			$user
		);

		$this->orderCreationService->fillOrderItems($order, $orderPreview);

		foreach ($order->getItems() as $orderItem) {
			$this->em->persist($orderItem);
		}

		$this->orderService->calculateTotalPrice($order);
		$this->em->persist($order);
		$this->em->flush();

		return $order;
	}

	/**
	 * @param int $orderId
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function edit($orderId, OrderData $orderData) {
		$order = $this->orderRepository->getById($orderId);
		$newOrderStatus = $this->orderStatusRepository->getById($orderData->statusId);
		$statusChanged = $order->getStatus()->getId() !== $orderData->statusId;
		$orderEditResult = $this->orderService->editOrder($order, $orderData, $newOrderStatus);

		foreach ($orderEditResult->getOrderItemsToCreate() as $orderItem) {
			$this->em->persist($orderItem);
		}
		foreach ($orderEditResult->getOrderItemsToDelete() as $orderItem) {
			$this->em->remove($orderItem);
		}

		$this->em->flush();
		if ($statusChanged) {
			$mailTemplate = $this->orderMailFacade
				->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
			if ($mailTemplate->isSendMail()) {
				$this->orderMailFacade->sendEmail($order);
			}

		}

		return $order;
	}

	/**
	 * @param string $confirmTextTemplate
	 * @param int $orderId
	 * @return string
	 */
	public function getOrderConfirmText($orderId) {
		$order = $this->getById($orderId);
		$confirmTextTemplate = $this->setting->get(Setting::ORDER_SUBMITTED_SETTING_NAME, $order->getDomainId());

		$variables = [
			OrderMailService::VARIABLE_TRANSPORT_INSTRUCTIONS => $order->getTransport()->getInstructions(),
			OrderMailService::VARIABLE_PAYMENT_INSTRUCTIONS => $order->getPayment()->getInstructions(),
		];

		return strtr($confirmTextTemplate, $variables);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\FrontOrderData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function prefillFrontOrderData(FrontOrderData $orderData, User $user) {
		$order = $this->orderRepository->findLastByUserId($user->getId());
		$this->orderCreationService->prefillFrontFormData($orderData, $user, $order);
	}

	/**
	 * @param int $orderId
	 */
	public function deleteById($orderId) {
		$order = $this->orderRepository->getById($orderId);
		$order->markAsDeleted();
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return array
	 */
	public function getCustomerOrderList(User $user) {
		return $this->orderRepository->getCustomerOrderList($user);
	}

	/**
	 * @param int $orderId
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getById($orderId) {
		return $this->orderRepository->getById($orderId);
	}

	/**
	 * @param string $urlHash
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getByUrlHashAndDomain($urlHash, $domainId) {
		return $this->orderRepository->getByUrlHashAndDomain($urlHash, $domainId);
	}

	/**
	 * @param string $orderNumber
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getByOrderNumberAndUser($orderNumber, User $user) {
		return $this->orderRepository->getByOrderNumberAndUser($orderNumber, $user);
	}

	/**
	 * @param \SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $quickSearchData
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getOrderListQueryBuilderByQuickSearchData(QuickSearchFormData $quickSearchData) {
		return $this->orderRepository->getOrderListQueryBuilderByQuickSearchData(
			$this->localization->getDefaultLocale(),
			$quickSearchData
		);
	}
}
