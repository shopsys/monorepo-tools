<?php

namespace SS6\ShopBundle\Model\Pricing;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_calculated_prices")
 * @ORM\Entity
 */
class ProductCalculatedPrice {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6, nullable=false)
	 */
	private $priceWithVat;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param type $priceWithVat
	 */
	public function __construct(Product $product, $priceWithVat) {
		$this->product = $product;
		$this->priceWithVat = $priceWithVat;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return string
	 */
	public function getPriceWithVat() {
		return $this->priceWithVat;
	}

}
