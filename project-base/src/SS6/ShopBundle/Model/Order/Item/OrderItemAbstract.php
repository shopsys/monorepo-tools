<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Order;

/**
 * @ORM\Table(name="order_items")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"payment" = "OrderPayment", "product" = "OrderProduct", "transport" = "OrderTransport"})
 */
abstract class OrderItemAbstract {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Order
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Order\Order", inversedBy="items")
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
	 */
	private $order;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $quantity;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param string $name
	 * @param string $price
	 * @param int $quantity
	 */
	public function __construct(Order $order, $name, $price, $quantity) {
		$this->order = $order; // Must be One-To-Many Bidirectional because of unnecessary join table
		$this->name = $name;
		$this->price = $price;
		$this->quantity = $quantity;
		$this->order->addItem($this); // call after setting attrs for recalc total price
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
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * @return string
	 */
	public function getTotalPrice() {
		return $this->price * $this->quantity;
	}

}
