<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\PriceCalculation as CartItemPriceCalculation;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserRepository;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Mail\OrderMailFacade;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderService;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;
use SS6\ShopBundle\Model\Payment\PriceCalculation as PaymentPriceCalculation;
use SS6\ShopBundle\Model\Transport\PriceCalculation as TransportPriceCalculation;

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
	 * @var \SS6\ShopBundle\Model\Customer\UserRepository
	 */
	private $userRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\PriceCalculation
	 */
	private $cartItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Mail\OrderMailFacade
	 */
	private $orderMailFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	public function __construct(
		EntityManager $em,
		OrderNumberSequenceRepository $orderNumberSequenceRepository,
		Cart $cart,
		OrderRepository $orderRepository,
		OrderService $orderService,
		UserRepository $userRepository,
		OrderStatusRepository $orderStatusRepository,
		CartItemPriceCalculation $cartItemPriceCalculation,
		PaymentPriceCalculation $paymentPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		OrderMailFacade $orderMailFacade,
		Domain $domain
	) {
		$this->em = $em;
		$this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
		$this->cart = $cart;
		$this->orderRepository = $orderRepository;
		$this->orderService = $orderService;
		$this->userRepository = $userRepository;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->cartItemPriceCalculation = $cartItemPriceCalculation;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->orderMailFacade = $orderMailFacade;
		$this->domain = $domain;
	}

	/**
	 * @param $orderData \SS6\ShopBundle\Model\Order\OrderData
	 * @param $user \SS6\ShopBundle\Model\Customer\User|null
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function createOrder(OrderData $orderData, User $user = null) {
		$orderStatus = $this->orderStatusRepository->getDefault();
		$orderNumber = $this->orderNumberSequenceRepository->getNextNumber();

		$order = $this->orderService->createOrder(
			$orderData,
			$orderNumber,
			$orderStatus,
			$user
		);

		$this->fillOrderItems($order, $this->cart);
		$this->orderService->calculateTotalPrice($order);
		$this->em->persist($order);
		$this->em->flush();

		return $order;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function fillOrderItems(Order $order, Cart $cart) {
		$locale = $this->domain->getDomainConfigById($order->getDomainId())->getLocale();
		$cartItems = $cart->getItems();
		foreach ($cartItems as $cartItem) {
			/* @var $cartItem \SS6\ShopBundle\Model\Cart\Item\CartItem */
			$cartItemPrice = $this->cartItemPriceCalculation->calculatePrice($cartItem);

			$orderItem = new OrderProduct(
				$order,
				$cartItem->getProduct()->getName(),
				$cartItemPrice->getUnitPriceWithoutVat(),
				$cartItemPrice->getUnitPriceWithVat(),
				$cartItem->getProduct()->getVat()->getPercent(),
				$cartItem->getQuantity(),
				$cartItem->getProduct()
			);
			$order->addItem($orderItem);
			$this->em->persist($orderItem);
		}

		$payment = $order->getPayment();
		$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment);
		$orderPayment = new OrderPayment(
			$order,
			$payment->getName(),
			$paymentPrice->getPriceWithoutVat(),
			$paymentPrice->getPriceWithVat(),
			$payment->getVat()->getPercent(),
			1,
			$payment
		);
		$order->addItem($orderPayment);
		$this->em->persist($orderPayment);

		$transport = $order->getTransport();
		$transportPrice = $this->transportPriceCalculation->calculatePrice($transport);
		$orderTransport = new OrderTransport(
			$order,
			$transport->getName($locale),
			$transportPrice->getPriceWithoutVat(),
			$transportPrice->getPriceWithVat(),
			$transport->getVat()->getPercent(),
			1,
			$transport
		);
		$order->addItem($orderTransport);
		$this->em->persist($orderTransport);
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
		$this->orderService->prefillFrontFormData($orderData, $user, $order);
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
	 * @return array
	 */
	public function getCustomerOrderListData(User $user) {
		return $this->orderRepository->getCustomerOrderListData($user);
	}

	/**
	 * @param int $orderId
	 * @return \SS6\ShopBundle\Model\Order\Order
	 */
	public function getById($orderId) {
		return $this->orderRepository->getById($orderId);
	}
}
