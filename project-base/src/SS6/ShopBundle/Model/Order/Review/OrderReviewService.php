<?php

namespace SS6\ShopBundle\Model\Order\Review;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderReviewService {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function __construct(Cart $cart) {
		$this->cart = $cart;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Review\Payment $payment
	 * @param \SS6\ShopBundle\Model\Order\Review\Transport $transport
	 * @return \SS6\ShopBundle\Model\Order\Review\OrderReview
	 */
	public function getOrderReview(Payment $payment = null, Transport $transport = null) {
		$orderReview = new OrderReview($this->cart, $payment, $transport);

		return $orderReview;
	}
}
