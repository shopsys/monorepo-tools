<?php

namespace SS6\ShopBundle\Model\Order\Item;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Pricing\Price;
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
	 * @param \SS6\ShopBundle\Model\Pricing\Price $price
	 * @param string $vatPercent
	 * @param int $quantity
	 * @param string $unitName
	 * @param string|null $catnum
	 * @param \SS6\ShopBundle\Model\Product\Product|null $product
	 */
	public function __construct(
		Order $order,
		$name,
		Price $price,
		$vatPercent,
		$quantity,
		$unitName,
		$catnum,
		Product $product = null
	) {
		parent::__construct(
			$order,
			$name,
			$price,
			$vatPercent,
			$quantity,
			$unitName,
			$catnum
		);

		if ($product !== null && $product->isMainVariant()) {
			throw new \SS6\ShopBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException();
		}

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
}
