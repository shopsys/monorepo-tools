<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

class OrderItemFactory implements OrderItemFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param string|null $unitName
     * @param string|null $catnum
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $product
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function createProduct(
        Order $order,
        string $name,
        Price $price,
        string $vatPercent,
        int $quantity,
        ?string $unitName,
        ?string $catnum,
        ?Product $product = null
    ): OrderItem {
        $classData = $this->entityNameResolver->resolve(OrderItem::class);

        $orderProduct = new $classData(
            $order,
            $name,
            $price,
            $vatPercent,
            $quantity,
            OrderItem::TYPE_PRODUCT,
            $unitName,
            $catnum
        );

        $orderProduct->setProduct($product);

        return $orderProduct;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function createPayment(
        Order $order,
        string $name,
        Price $price,
        string $vatPercent,
        int $quantity,
        Payment $payment
    ): OrderItem {
        $classData = $this->entityNameResolver->resolve(OrderItem::class);

        $orderPayment = new $classData(
            $order,
            $name,
            $price,
            $vatPercent,
            $quantity,
            OrderItem::TYPE_PAYMENT,
            null,
            null
        );

        $orderPayment->setPayment($payment);
        return $orderPayment;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderItem
     */
    public function createTransport(
        Order $order,
        string $name,
        Price $price,
        string $vatPercent,
        int $quantity,
        Transport $transport
    ): OrderItem {
        $classData = $this->entityNameResolver->resolve(OrderItem::class);

        $orderTransport = new $classData(
            $order,
            $name,
            $price,
            $vatPercent,
            $quantity,
            OrderItem::TYPE_TRANSPORT,
            null,
            null
        );

        $orderTransport->setTransport($transport);
        return $orderTransport;
    }
}
