<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Entity
 */
class OrderProduct extends OrderItem {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product|null
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=true, name="product_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $product;

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param string $name
	 * @param string $priceWithoutVat
	 * @param string $priceWithVat
	 * @param string $vatPercent
	 * @param int $quantity
	 * @param string|null $catnum
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	public function __construct(
		Order $order,
		$name,
		$priceWithoutVat,
		$priceWithVat,
		$vatPercent,
		$quantity,
		$catnum,
		Product $product = null
	) {
		parent::__construct(
			$order,
			$name,
			$priceWithoutVat,
			$priceWithVat,
			$vatPercent,
			$quantity,
			$catnum
		);
		$this->product = $product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product|null
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return bool
	 */
	public function hasProduct() {
		return $this->product !== null;
	}

	/**
	 * @inheritdoc
	 */
	public function edit(OrderItemData $orderItemData) {
		$name = $this->name;
		parent::edit($orderItemData);

		if ($this->hasProduct()) {
			$this->name = $name;
		}
	}

}
