<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="products_top")
 * @ORM\Entity
 */
class TopProduct {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 * @ORM\Id
	 */
	private $product;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 */
	private $domainId;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 */
	public function __construct(Product $product, $domainId) {
		$this->product = $product;
		$this->domainId = $domainId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

}
