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
	private $items;

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
		foreach ($cart->getItems() as $item) {
			$orderReviewItem = new OrderReviewItem(
				$item->getName(),
				OrderReviewItem::TYPE_PRODUCT,
				$item->getQuantity(),
				$item->getTotalPrice()
			);
			$this->items[] = $orderReviewItem;
			$this->totalPrice += $item->getTotalPrice();
		}

		if ($transport !== null) {
			$this->items[] = new OrderReviewItem($transport->getName(), OrderReviewItem::TYPE_TRANSPORT, 1, $transport->getPrice());
			$this->totalPrice += $transport->getPrice();
		}

		if ($payment !== null) {
			$this->items[] = new OrderReviewItem($payment->getName(), OrderReviewItem::TYPE_PAYMENT, 1, $payment->getPrice());
			$this->totalPrice += $payment->getPrice();
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Review\OrderReviewItem[]
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 *
	 * @return string
	 */
	public function getTotalPrice() {
		return $this->totalPrice;
	}

}
