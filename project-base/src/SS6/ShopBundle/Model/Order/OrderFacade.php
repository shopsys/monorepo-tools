<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserRepository;
use SS6\ShopBundle\Model\Order\Mail\OrderMailFacade;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderCreationService;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderService;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

use SS6\ShopBundle\Model\Order\OrderHashGeneratorRepository;

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
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

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

	public function __construct(
		EntityManager $em,
		OrderNumberSequenceRepository $orderNumberSequenceRepository,
		Cart $cart,
		OrderRepository $orderRepository,
		OrderService $orderService,
		OrderCreationService $orderCreationService,
		UserRepository $userRepository,
		OrderStatusRepository $orderStatusRepository,
		OrderMailFacade $orderMailFacade,
		OrderHashGeneratorRepository $orderHashGeneratorRepository
	) {
		$this->em = $em;
		$this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
		$this->cart = $cart;
		$this->orderRepository = $orderRepository;
		$this->orderService = $orderService;
		$this->orderCreationService = $orderCreationService;
		$this->userRepository = $userRepository;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderMailFacade = $orderMailFacade;
		$this->orderHashGeneratorRepository = $orderHashGeneratorRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Item\QuantifiedItem[] $quantifiedItems
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrder(OrderData $orderData, array $quantifiedItems, User $user = null) {
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

		$this->orderCreationService->fillOrderItems($order, $quantifiedItems);

		foreach ($order->getItems() as $orderItem) {
			$this->em->persist($orderItem);
		}

		$this->orderService->calculateTotalPrice($order);
		$this->em->persist($order);
		$this->em->flush();

		return $order;
	}

	/**
	 * @param $orderData \SS6\ShopBundle\Model\Order\OrderData
	 * @param $user \SS6\ShopBundle\Model\Customer\User|null
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrderFromCart(OrderData $orderData, User $user = null) {
		return $this->createOrder(
			$orderData,
			$this->cart->getProductQuantifiedItems(),
			$user
		);
	}

	/**
	 *
	 * @param int $orderId
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function edit($orderId, OrderData $orderData) {
		$order = $this->orderRepository->getById($orderId);
		$orderStatus = $this->orderStatusRepository->getById($orderData->getStatusId());
		$user = null;
		if ($orderData->getCustomerId() !== null) {
			$user = $this->userRepository->getUserById($orderData->getCustomerId());
		}
		$statusChanged = $order->getStatus()->getId() !== $orderData->getStatusId();
		$orderEditResult = $this->orderService->editOrder($order, $orderData, $orderStatus, $user);

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
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function prefillOrderData(OrderData $orderData, User $user) {
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
	 * @param \SS6\ShopBundle\Model\Order\SS6\ShopBundle\Model\Customer\User $user
	 * @param string $locale
	 * @return array
	 */
	public function getCustomerOrderListData(User $user, $locale) {
		return $this->orderRepository->getCustomerOrderListData($user, $locale);
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
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getByUrlHash($urlHash) {
		return $this->orderRepository->getByUrlHash($urlHash);
	}

	/**
	 * @param string $orderNumber
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getByOrderNumber($orderNumber) {
		return $this->orderRepository->getByOrderNumber($orderNumber);
	}
}
