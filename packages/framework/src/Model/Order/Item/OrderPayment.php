<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Price;

/**
 * @ORM\Entity
 */
class OrderPayment extends OrderItem
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Payment\Payment")
     * @ORM\JoinColumn(nullable=true)
     */
    private $payment;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     */
    public function __construct(
        Order $order,
        $name,
        Price $price,
        $vatPercent,
        $quantity,
        Payment $payment
    ) {
        parent::__construct(
            $order,
            $name,
            $price,
            $vatPercent,
            $quantity,
            null,
            null
        );
        $this->payment = $payment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderPaymentData
     */
    public function edit(OrderItemData $orderPaymentData)
    {
        if ($orderPaymentData instanceof OrderPaymentData) {
            $this->payment = $orderPaymentData->payment;
            parent::edit($orderPaymentData);
        } else {
            throw new \Shopsys\FrameworkBundle\Model\Order\Item\Exception\InvalidArgumentException(
                'Instance of ' . OrderPaymentData::class . ' is required as argument.'
            );
        }
    }
}
