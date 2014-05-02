<?php

namespace SS6\ShopBundle\Model\Order;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Form\Front\Order\OrderFormData;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Customer\UserIdentity;
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
	 * @param $userIdentity \SS6\ShopBundle\Model\Customer\UserIdentity|null
	 */
	public function createOrder(OrderFormData $orderFormData, UserIdentity $userIdentity = null) {
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
			$userIdentity,
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

		$cartItems = $this->cart->getItems();
		foreach ($cartItems as $cartItem) {
			/* @var $cartItem \SS6\ShopBundle\Model\Cart\CartItem */
			$orderItem = new OrderItem($order,
				$cartItem->getProduct()->getName(),
				$cartItem->getProduct()->getPrice(),
				$cartItem->getQuantity(),
				$cartItem->getProduct()
			);
			$order->addItem($orderItem);
			$this->em->persist($orderItem);
			$this->em->remove($cartItem);
		}

		$this->em->persist($order);
		$this->em->flush();
	}
}
