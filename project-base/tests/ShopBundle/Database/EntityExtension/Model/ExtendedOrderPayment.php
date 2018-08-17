<?php

namespace Tests\ShopBundle\Database\EntityExtension\Model;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderPaymentData;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Payment\Payment;

/**
 * @ORM\Entity
 *
 * Base of this class is a copy of OrderPayment entity from FrameworkBundle
 * Reason is described in /project-base/docs/wip_glassbox/entity-extension.md
 *
 * @see \Shopsys\FrameworkBundle\Model\Order\Item\OrderPayment
 */
class ExtendedOrderPayment extends ExtendedOrderItem
{
    /**
     * @var \Shopsys\ShopBundle\Model\Payment\Payment
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Payment\Payment")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $payment;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $paymentStringField;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param \Shopsys\ShopBundle\Model\Payment\Payment $payment
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
     * @return \Shopsys\ShopBundle\Model\Payment\Payment
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

    /**
     * @return string|null
     */
    public function getPaymentStringField()
    {
        return $this->paymentStringField;
    }

    /**
     * @param string|null $paymentStringField
     */
    public function setPaymentStringField($paymentStringField)
    {
        $this->paymentStringField = $paymentStringField;
    }
}
