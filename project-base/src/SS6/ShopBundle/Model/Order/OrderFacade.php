<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Form\Front\Order\OrderFormData;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository;

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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\OrderNumberSequenceRepository $orderNumberSequenceRepository
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function __construct(EntityManager $em, OrderNumberSequenceRepository $orderNumberSequenceRepository,
		Cart $cart) {
		$this->em = $em;
		$this->orderNumberSequenceRepository = $orderNumberSequenceRepository;
		$this->cart = $cart;
	}

	/**
	 * @param $orderFormData \SS6\ShopBundle\Form\Front\Order\OrderFormData
	 * @param $user \SS6\ShopBundle\Model\Customer\User|null
	 */
	public function createOrder(OrderFormData $orderFormData, User $user = null) {
		$order = new Order(
			$this->orderNumberSequenceRepository->getNextNumber(),
			$orderFormData->getTransport(),
			$orderFormData->getPayment(),
			$orderFormData->getFirstName(),
			$orderFormData->getLastName(),
			$orderFormData->getEmail(),
			$orderFormData->getTelephone(),
			$orderFormData->getStreet(),
			$orderFormData->getCity(),
			$orderFormData->getZip(),
			$user,
			$orderFormData->getCompanyName(),
			$orderFormData->getCompanyNumber(),
			$orderFormData->getCompanyTaxNumber(),
			$orderFormData->getDeliveryFirstName(),
			$orderFormData->getDeliveryLastName(),
			$orderFormData->getDeliveryCompanyName(),
			$orderFormData->getDeliveryTelephone(),
			$orderFormData->getDeliveryStreet(),
			$orderFormData->getDeliveryCity(),
			$orderFormData->getDeliveryZip(),
			$orderFormData->getNote());

		$this->fillOrderItems($order, $this->cart);

		$this->em->persist($order);
		$this->em->flush();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function fillOrderItems(Order $order, Cart $cart) {
		$cartItems = $cart->getItems();
		foreach ($cartItems as $cartItem) {
			/* @var $cartItem \SS6\ShopBundle\Model\Cart\CartItem */
			$orderItem = new OrderProduct($order,
				$cartItem->getProduct()->getName(),
				$cartItem->getProduct()->getPrice(),
				$cartItem->getQuantity(),
				$cartItem->getProduct()
			);
			$order->addItem($orderItem);
			$this->em->persist($orderItem);
			$this->em->remove($cartItem);
		}

		$payment = $order->getPayment();
		$orderPayment = new OrderPayment($order,
			$payment->getName(),
			$payment->getPrice(),
			1,
			$payment
		);
		$order->addItem($orderPayment);
		$this->em->persist($orderPayment);

		$transport = $order->getTransport();
		$orderTransport = new OrderTransport($order,
			$transport->getName(),
			$transport->getPrice(),
			1,
			$transport
		);
		$order->addItem($orderTransport);
		$this->em->persist($orderTransport);
	}
}
