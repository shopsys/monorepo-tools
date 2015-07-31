<?php

namespace SS6\ShopBundle\Model\Product\Accessory;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_accessories")
 * @ORM\Entity
 */
class ProductAccessory {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 * @ORM\Id
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="accessory_product_id", referencedColumnName="id", onDelete="CASCADE")
	 * @ORM\Id
	 */
	private $accessory;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $position;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\Product $accessory
	 * @param int $position
	 */
	public function __construct(Product $product, Product $accessory, $position) {
		$this->product = $product;
		$this->accessory = $accessory;
		$this->position = $position;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getAccessory() {
		return $this->accessory;
	}

	/**
	 * @return int
	 */
	public function getPosition() {
		return $this->position;
	}
}
