<?php

namespace Shopsys\FrameworkBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException;
use Shopsys\FrameworkBundle\Model\Order\Item\Exception\WrongItemTypeException;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

/**
 * @ORM\Table(name="order_items")
 * @ORM\Entity
 */
class OrderItem
{
    public const
        TYPE_PAYMENT = 'payment',
        TYPE_PRODUCT = 'product',
        TYPE_TRANSPORT = 'transport';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Order
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Order\Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    protected $order;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     *
     * @ORM\Column(type="money", precision=20, scale=6)
     */
    protected $priceWithoutVat;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money
     *
     * @ORM\Column(type="money", precision=20, scale=6)
     */
    protected $priceWithVat;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6)
     */
    protected $vatPercent;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $quantity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $unitName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $catnum;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Transport\Transport")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $transport;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Payment\Payment")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product")
     * @ORM\JoinColumn(nullable=true, name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $product;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param string $type
     * @param string|null $unitName
     * @param string|null $catnum
     */
    public function __construct(
        Order $order,
        $name,
        Price $price,
        $vatPercent,
        $quantity,
        $type,
        $unitName,
        $catnum
    ) {
        $this->order = $order; // Must be One-To-Many Bidirectional because of unnecessary join table
        $this->name = $name;
        $this->priceWithoutVat = Money::fromValue($price->getPriceWithoutVat());
        $this->priceWithVat = Money::fromValue($price->getPriceWithVat());
        $this->vatPercent = $vatPercent;
        $this->quantity = $quantity;
        $this->type = $type;
        $this->unitName = $unitName;
        $this->catnum = $catnum;
        $this->order->addItem($this); // call after setting attrs for recalc total price
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat->toValue();
    }

    /**
     * @return string
     */
    public function getPriceWithVat()
    {
        return $this->priceWithVat->toValue();
    }

    /**
     * @return string
     */
    public function getVatPercent()
    {
        return $this->vatPercent;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return string|null
     */
    public function getUnitName()
    {
        return $this->unitName;
    }

    /**
     * @return string|null
     */
    public function getCatnum()
    {
        return $this->catnum;
    }

    /**
     * @return string
     */
    public function getTotalPriceWithVat()
    {
        return $this->priceWithVat->multiply((string)$this->quantity)->toValue();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData $orderItemData
     */
    public function edit(OrderItemData $orderItemData)
    {
        $this->name = $orderItemData->name;
        $this->priceWithoutVat = Money::fromValue($orderItemData->priceWithoutVat);
        $this->priceWithVat = Money::fromValue($orderItemData->priceWithVat);
        $this->vatPercent = $orderItemData->vatPercent;
        $this->quantity = $orderItemData->quantity;
        $this->unitName = $orderItemData->unitName;
        $this->catnum = $orderItemData->catnum;

        if ($this->isTypeTransport()) {
            $this->transport = $orderItemData->transport;
        }

        if ($this->isTypePayment()) {
            $this->payment = $orderItemData->payment;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport $transport
     */
    public function setTransport(Transport $transport): void
    {
        $this->checkTypeTransport();
        $this->transport = $transport;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Transport\Transport
     */
    public function getTransport(): Transport
    {
        $this->checkTypeTransport();
        return $this->transport;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->checkTypePayment();
        $this->payment = $payment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getPayment(): Payment
    {
        $this->checkTypePayment();
        return $this->payment;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public function getProduct(): ?Product
    {
        $this->checkTypeProduct();
        return $this->product;
    }

    /**
     * @return bool
     */
    public function hasProduct()
    {
        $this->checkTypeProduct();
        return $this->product !== null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product|null $product
     */
    public function setProduct(?Product $product): void
    {
        $this->checkTypeProduct();

        if ($product !== null && $product->isMainVariant()) {
            throw new MainVariantCannotBeOrderedException();
        }

        $this->product = $product;
    }

    /**
     * @return bool
     */
    public function isTypeProduct(): bool
    {
        return $this->type === self::TYPE_PRODUCT;
    }

    /**
     * @return bool
     */
    public function isTypePayment(): bool
    {
        return $this->type === self::TYPE_PAYMENT;
    }

    /**
     * @return bool
     */
    public function isTypeTransport(): bool
    {
        return $this->type === self::TYPE_TRANSPORT;
    }

    protected function checkTypeTransport(): void
    {
        if (!$this->isTypeTransport()) {
            throw new WrongItemTypeException(self::TYPE_TRANSPORT, $this->type);
        }
    }

    protected function checkTypePayment(): void
    {
        if (!$this->isTypePayment()) {
            throw new WrongItemTypeException(self::TYPE_PAYMENT, $this->type);
        }
    }

    protected function checkTypeProduct(): void
    {
        if (!$this->isTypeProduct()) {
            throw new WrongItemTypeException(self::TYPE_PRODUCT, $this->type);
        }
    }
}
