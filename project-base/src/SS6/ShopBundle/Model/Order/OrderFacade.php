<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use Exception;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;
use SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use SS6\ShopBundle\Model\Cart\CartFacade;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\CustomerFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserRepository;
use SS6\ShopBundle\Model\Heureka\HeurekaShopCertificationFactory;
use SS6\ShopBundle\Model\Localization\Localization;
use SS6\ShopBundle\Model\Order\Item\OrderProductFacade;
use SS6\ShopBundle\Model\Order\Mail\OrderMailFacade;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderCreationService;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderHashGeneratorRepository;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use SS6\ShopBundle\Model\Order\OrderService;
use SS6\ShopBundle\Model\Order\Preview\OrderPreview;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use Symfony\Bridge\Monolog\Logger;

class OrderFacade {

	const VARIABLE_NUMBER = '{number}';
	const VARIABLE_ORDER_DETAIL_URL = '{order_detail_url}';
	const VARIABLE_PAYMENT_INSTRUCTIONS = '{payment_instructions}';
	const VARIABLE_TRANSPORT_INSTRUCTIONS = '{transport_instructions}';

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
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Localization
	 */
	private $localization;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade
	 */
	private $administratorFrontSecurityFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
	 */
	private $currentPromoCodeFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartFacade
	 */
	private $cartFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerFacade
	 */
	private $customerFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Preview\OrderPreviewFactory
	 */
	private $orderPreviewFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderProductFacade
	 */
	private $orderProductFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Heureka\HeurekaShopCertificationFactory
	 */
	private $heurekaShopCertificationFactory;

	/**
	 * @var \Symfony\Bridge\Monolog\Logger
	 */
	private $logger;

	public function __construct(
		Logger $logger,
		EntityManager $em,
		OrderNumberSequenceRepository $orderNumberSequenceRepository,
		OrderRepository $orderRepository,
		OrderService $orderService,
		OrderCreationService $orderCreationService,
		UserRepository $userRepository,
		OrderStatusRepository $orderStatusRepository,
		OrderMailFacade $orderMailFacade,
		OrderHashGeneratorRepository $orderHashGeneratorRepository,
		Setting $setting,
		Localization $localization,
		AdministratorFrontSecurityFacade $administratorFrontSecurityFacade,
		CurrentPromoCodeFacade $currentPromoCodeFacade,
		CartFacade $cartFacade,
		CustomerFacade $customerFacade,
		CurrentCustomer $currentCustomer,
		OrderPreviewFactory $orderPreviewFactory,
		OrderProductFacade $orderProductFacade,
		DomainRouterFactory $domainRouterFactory,
		HeurekaShopCertificationFactory $heurekaShopCertificationFactory
	) {
		$this->logger = $logger;
		$this->em = $em;
		$this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
		$this->orderRepository = $orderRepository;
		$this->orderService = $orderService;
		$this->orderCreationService = $orderCreationService;
		$this->userRepository = $userRepository;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderMailFacade = $orderMailFacade;
		$this->orderHashGeneratorRepository = $orderHashGeneratorRepository;
		$this->setting = $setting;
		$this->localization = $localization;
		$this->administratorFrontSecurityFacade = $administratorFrontSecurityFacade;
		$this->currentPromoCodeFacade = $currentPromoCodeFacade;
		$this->cartFacade = $cartFacade;
		$this->customerFacade = $customerFacade;
		$this->currentCustomer = $currentCustomer;
		$this->orderPreviewFactory = $orderPreviewFactory;
		$this->orderProductFacade = $orderProductFacade;
		$this->domainRouterFactory = $domainRouterFactory;
		$this->heurekaShopCertificationFactory = $heurekaShopCertificationFactory;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrder(OrderData $orderData, OrderPreview $orderPreview, User $user = null) {
		$orderNumber = $this->orderNumberSequenceRepository->getNextNumber();
		$orderUrlHash = $this->orderHashGeneratorRepository->getUniqueHash();
		$toFlush = [];

		$this->setOrderDataAdministrator($orderData);

		$order = new Order(
			$orderData,
			$orderNumber,
			$orderUrlHash,
			$user
		);
		$toFlush[] = $order;

		$this->orderCreationService->fillOrderItems($order, $orderPreview);

		foreach ($order->getItems() as $orderItem) {
			$this->em->persist($orderItem);
			$toFlush[] = $orderItem;
		}

		$this->orderService->calculateTotalPrice($order);
		$this->em->persist($order);
		$this->em->flush($toFlush);

		return $order;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrderFromFront(OrderData $orderData) {
		$orderData->status = $this->orderStatusRepository->getDefault();
		$orderPreview = $this->orderPreviewFactory->createForCurrentUser($orderData->transport, $orderData->payment);
		$user = $this->currentCustomer->findCurrentUser();

		$order = $this->createOrder($orderData, $orderPreview, $user);
		$this->orderProductFacade->subtractOrderProductsFromStock($order->getProductItems());

		$this->cartFacade->cleanCart();
		$this->currentPromoCodeFacade->removeEnteredPromoCode();
		if ($user instanceof User) {
			$this->customerFacade->amendCustomerDataFromOrder($user, $order);
		}
		try {
			$heurekaShopCertification = $this->heurekaShopCertificationFactory->create($order);
			$heurekaShopCertification->logOrder();
		} catch (\SS6\ShopBundle\Model\Heureka\Exception\LocaleNotSupportedException $ex) {
			$this->logError($ex, $order);
		} catch (\Heureka\ShopCertification\Exception $ex) {
			$this->logError($ex, $order);
		}

		return $order;
	}

	/**
	 * @param int $orderId
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function edit($orderId, OrderData $orderData) {
		$order = $this->orderRepository->getById($orderId);
		$originalOrderStatus = $order->getStatus();
		$orderEditResult = $this->orderService->editOrder($order, $orderData);

		foreach ($orderEditResult->getOrderItemsToCreate() as $orderItem) {
			$this->em->persist($orderItem);
		}
		foreach ($orderEditResult->getOrderItemsToDelete() as $orderItem) {
			$this->em->remove($orderItem);
		}

		$this->em->flush();
		if ($orderEditResult->isStatusChanged()) {
			$mailTemplate = $this->orderMailFacade
				->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
			if ($mailTemplate->isSendMail()) {
				$this->orderMailFacade->sendEmail($order);
			}
			if ($originalOrderStatus->getType() === OrderStatus::TYPE_CANCELED) {
				$this->orderProductFacade->subtractOrderProductsFromStock($order->getProductItems());
			}
			if ($orderData->status->getType() === OrderStatus::TYPE_CANCELED) {
				$this->orderProductFacade->addOrderProductsToStock($order->getProductItems());
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
		$orderDetailUrl = $this->orderService->getOrderDetailUrl($order);
		$confirmTextTemplate = $this->setting->getForDomain(Setting::ORDER_SUBMITTED_SETTING_NAME, $order->getDomainId());

		$variables = [
			self::VARIABLE_TRANSPORT_INSTRUCTIONS => $order->getTransport()->getInstructions(),
			self::VARIABLE_PAYMENT_INSTRUCTIONS => $order->getPayment()->getInstructions(),
			self::VARIABLE_ORDER_DETAIL_URL => $orderDetailUrl,
			self::VARIABLE_NUMBER => $order->getNumber(),
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
		if ($order->getStatus()->getType() !== OrderStatus::TYPE_CANCELED) {
			$this->orderProductFacade->addOrderProductsToStock($order->getProductItems());
		}
		$order->markAsDeleted();
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Model\Order\Order[]
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

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 */
	private function setOrderDataAdministrator(OrderData $orderData) {
		if ($this->administratorFrontSecurityFacade->isAdministratorLoggedAsCustomer()) {
			try {
				$currentAdmin = $this->administratorFrontSecurityFacade->getCurrentAdministrator();
				$orderData->createdAsAdministrator = $currentAdmin;
				$orderData->createdAsAdministratorName = $currentAdmin->getRealName();
			} catch (\SS6\ShopBundle\Model\Administrator\Security\Exception\AdministratorIsNotLoggedException $ex) {
			}
		}
	}

	/**
	 * @param \Exception $ex
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	private function logError(Exception $ex, Order $order) {
		$message = 'Sending order (ID = "' . $order->getId() . '") to Heureka failed - ' . get_class($ex) . ': ' . $ex->getMessage();
		$this->logger->error($message);
	}

}
