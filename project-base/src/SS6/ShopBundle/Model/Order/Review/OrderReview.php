<?php

namespace SS6\ShopBundle\Model\Order\Review;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Order\Review\OrderReviewItem;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderReview {

	/**
	 * @var \SS6\ShopBundle\Model\Order\Review\OrderReviewItem[]
	 */
	private $orderReviewItems;

	/**
	 * @var string
	 */
	private $totalPrice = 0;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 */
	public function __construct(Cart $cart, Payment $payment = null, Transport $transport = null) {
		foreach ($cart->getItems() as $cartItem) {
			$cartItemTotalPrice = $cartItem->getProduct()->getPrice() * $cartItem->getQuantity();

			$orderReviewItem = new OrderReviewItem(
				$cartItem->getName(),
				OrderReviewItem::TYPE_PRODUCT,
				$cartItem->getQuantity(),
				$cartItemTotalPrice
			);
			$this->orderReviewItems[] = $orderReviewItem;
			$this->totalPrice += $cartItemTotalPrice;
		}

		if ($transport !== null) {
			$this->orderReviewItems[] = new OrderReviewItem(
				$transport->getName(),
				OrderReviewItem::TYPE_TRANSPORT,
				1,
				$transport->getPrice()
			);
			$this->totalPrice += $transport->getPrice();
		}

		if ($payment !== null) {
			$this->orderReviewItems[] = new OrderReviewItem(
				$payment->getName(),
				OrderReviewItem::TYPE_PAYMENT,
				1,
				$payment->getPrice()
			);
			$this->totalPrice += $payment->getPrice();
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Review\OrderReviewItem[]
	 */
	public function getItems() {
		return $this->orderReviewItems;
	}

	/**
	 *
	 * @return string
	 */
	public function getTotalPrice() {
		return $this->totalPrice;
	}

}
