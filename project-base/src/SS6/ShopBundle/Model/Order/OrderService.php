<?php

namespace SS6\ShopBundle\Model\Order;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderService {

	const DEFAULT_QUANTITY = 1;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	public function __construct(
		Domain $domain,
		OrderItemPriceCalculation $orderItemPriceCalculation,
		OrderPriceCalculation $orderPriceCalculation,
		DomainRouterFactory $domainRouterFactory
	) {
		$this->domain = $domain;
		$this->orderItemPriceCalculation = $orderItemPriceCalculation;
		$this->orderPriceCalculation = $orderPriceCalculation;
		$this->domainRouterFactory = $domainRouterFactory;
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
					new Price(
						$orderItemData->priceWithoutVat,
						$orderItemData->priceWithVat
					),
					$orderItemData->vatPercent,
					$orderItemData->quantity,
					$orderItemData->unitName,
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
			$productPrice,
			$product->getVat()->getPercent(),
			self::DEFAULT_QUANTITY,
			$product->getUnit()->getName($orderDomainConfig->getLocale()),
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

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return string
	 */
	public function getOrderDetailUrl(Order $order) {
		return $this->domainRouterFactory->getRouter($order->getDomainId())->generate(
			'front_customer_order_detail_unregistered',
			['urlHash' => $order->getUrlHash()],
			UrlGeneratorInterface::ABSOLUTE_URL
		);
	}

}
