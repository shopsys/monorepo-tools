<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Pricing\Price;

/**
 * @ORM\Table(name="order_items")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"payment" = "OrderPayment", "product" = "OrderProduct", "transport" = "OrderTransport"})
 */
abstract class OrderItem
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Order
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Order\Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6)
     */
    protected $priceWithoutVat;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6)
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
    private $catnum;

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Order $order
     * @param string $name
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $price
     * @param string $vatPercent
     * @param int $quantity
     * @param string|null $unitName
     * @param string|null $catnum
     */
    public function __construct(
        Order $order,
        $name,
        Price $price,
        $vatPercent,
        $quantity,
        $unitName,
        $catnum
    ) {
        $this->order = $order; // Must be One-To-Many Bidirectional because of unnecessary join table
        $this->name = $name;
        $this->priceWithoutVat = $price->getPriceWithoutVat();
        $this->priceWithVat = $price->getPriceWithVat();
        $this->vatPercent = $vatPercent;
        $this->quantity = $quantity;
        $this->unitName = $unitName;
        $this->catnum = $catnum;
        $this->order->addItem($this); // call after setting attrs for recalc total price
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Order\Order
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPriceWithoutVat() {
        return $this->priceWithoutVat;
    }

    /**
     * @return string
     */
    public function getPriceWithVat() {
        return $this->priceWithVat;
    }

    /**
     * @return string
     */
    public function getVatPercent() {
        return $this->vatPercent;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @return string|null
     */
    public function getUnitName() {
        return $this->unitName;
    }

    /**
     * @return string|null
     */
    public function getCatnum() {
        return $this->catnum;
    }

    /**
     * @return string
     */
    public function getTotalPriceWithVat() {
        return $this->priceWithVat * $this->quantity;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Order\Item\OrderItemData $orderItemData
     */
    public function edit(OrderItemData $orderItemData) {
        $this->name = $orderItemData->name;
        $this->priceWithoutVat = $orderItemData->priceWithoutVat;
        $this->priceWithVat = $orderItemData->priceWithVat;
        $this->vatPercent = $orderItemData->vatPercent;
        $this->quantity = $orderItemData->quantity;
        $this->unitName = $orderItemData->unitName;
        $this->catnum = $orderItemData->catnum;
    }
}
