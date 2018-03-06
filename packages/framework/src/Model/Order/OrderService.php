<?php

namespace Shopsys\FrameworkBundle\Model\Order;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OrderService
{
    const DEFAULT_QUANTITY = 1;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemPriceCalculation
     */
    private $orderItemPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderPriceCalculation
     */
    private $orderPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\DomainRouterFactory
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @return \Shopsys\FrameworkBundle\Model\Order\OrderEditResult
     */
    public function editOrder(Order $order, OrderData $orderData)
    {
        $orderTransportData = $orderData->orderTransport;
        $orderTransportData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderTransportData);
        $orderPaymentData = $orderData->orderPayment;
        $orderPaymentData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($orderPaymentData);

        $statusChanged = $order->getStatus() !== $orderData->status;
        $order->edit($orderData);

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
        foreach ($orderData->getNewItemsWithoutTransportAndPayment() as $newOrderItemData) {
            $newOrderItemData->priceWithoutVat = $this->orderItemPriceCalculation->calculatePriceWithoutVat($newOrderItemData);
            $newOrderItem = new OrderProduct(
                $order,
                $newOrderItemData->name,
                new Price(
                    $newOrderItemData->priceWithoutVat,
                    $newOrderItemData->priceWithVat
                ),
                $newOrderItemData->vatPercent,
                $newOrderItemData->quantity,
                $newOrderItemData->unitName,
                $newOrderItemData->catnum
            );
            $orderItemsToCreate[] = $newOrderItem;
        }

        $this->calculateTotalPrice($order);

        return new OrderEditResult($orderItemsToCreate, $orderItemsToDelete, $statusChanged);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productPrice
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct
     */
    public function createOrderProductInOrder(Order $order, Product $product, Price $productPrice)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    public function calculateTotalPrice(Order $order)
    {
        $orderTotalPrice = $this->orderPriceCalculation->getOrderTotalPrice($order);
        $order->setTotalPrice($orderTotalPrice);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @return string
     */
    public function getOrderDetailUrl(Order $order)
    {
        return $this->domainRouterFactory->getRouter($order->getDomainId())->generate(
            'front_customer_order_detail_unregistered',
            ['urlHash' => $order->getUrlHash()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
