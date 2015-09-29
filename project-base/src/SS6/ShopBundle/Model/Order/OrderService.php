<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Product;

class OrderService {

	const DEFAULT_QUANTITY = 1;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation
	 */
	private $orderItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderPriceCalculation
	 */
	private $orderPriceCalculation;

	public function __construct(
		Domain $domain,
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation
	) {
		$this->domain = $domain;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 * @return \SS6\ShopBundle\Model\Order\OrderEditResult
	 */
	public function editOrder(Order $order, OrderData $orderData, OrderStatus $orderStatus) {
		$orderTransportData = $orderData->orderTransport;
		$orderTransportData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderTransportData);
		$orderPaymentData = $orderData->orderPayment;
		$orderPaymentData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderPaymentData);

		$statusChanged = $order->getStatus()->getId() !== $orderData->statusId;
		$order->edit(
			$orderData,
			$orderStatus
		);

		$orderItemsWithoutTransportAndPaymentData = $orderData->itemsWithoutTransportAndPayment;

		$orderItemsToDelete = [];
		foreach ($order->getItemsWithoutTransportAndPayment() as $orderItem) {
			if (array_key_exists($orderItem->getId(), $orderItemsWithoutTransportAndPaymentData)) {
				$orderItemData = $orderItemsWithoutTransportAndPaymentData[$orderItem->getId()];
				$orderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
				$orderItem->edit($orderItemData);
			} else {
				$order->removeItem($orderItem);
				$orderItemsToDelete[] = $orderItem;
			}
		}

		$orderItemsToCreate = [];
		foreach ($orderItemsWithoutTransportAndPaymentData as $index => $orderItemData) {
			if (strpos($index, 'new_') === 0) {
				$orderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);
				$orderItem = new OrderProduct(
					$order,
					$orderItemData->name,
					$orderItemData->priceWithoutVat,
					$orderItemData->priceWithVat,
					$orderItemData->vatPercent,
					$orderItemData->quantity,
					$orderItemData->catnum
				);
				$orderItemsToCreate[] = $orderItem;
			}
		}

		$this->calculateTotalPrice($order);

		return new OrderEditResult($orderItemsToCreate, $orderItemsToDelete, $statusChanged);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Price $productPrice
	 * @return \SS6\ShopBundle\Model\Order\Item\OrderProduct
	 */
	public function createOrderProductInOrder(Order $order, Product $product, Price $productPrice) {
		$orderDomainConfig = $this->domain->getDomainConfigById($order->getDomainId());

		$orderProduct = new OrderProduct(
			$order,
			$product->getName($orderDomainConfig->getLocale()),
			$productPrice->getPriceWithoutVat(),
			$productPrice->getPriceWithVat(),
			$product->getVat()->getPercent(),
			self::DEFAULT_QUANTITY,
			$product->getCatnum(),
			$product
		);

		$order->addItem($orderProduct);
		$this->calculateTotalPrice($order);

		return $orderProduct;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	public function calculateTotalPrice(Order $order) {
		$orderTotalPrice = $this->orderPriceCalculation->getOrderTotalPrice($order);
		$order->setTotalPrice($orderTotalPrice);
	}

}
