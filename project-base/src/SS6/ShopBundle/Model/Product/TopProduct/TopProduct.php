<?php

namespace SS6\ShopBundle\Model\Product\TopProduct;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\TopProduct\TopProductData;

/**
 * @ORM\Table(
 *	name="products_top",
 *	uniqueConstraints={
 *		@ORM\UniqueConstraint(name="product_domain_unique",columns={"product_id", "domain_id"})
 *	},
 *	indexes={
 *		@ORM\Index(name="idx_entity_id_type", columns={"product_id", "domain_id"})
 *	}
 * )
 * @ORM\Entity
 */
class TopProduct {

	/**
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 */
	public function __construct($domainId, TopProductData $topProductData) {
		$this->product = $topProductData->product;
		$this->domainId = $domainId;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\TopProduct\TopProductData $topProductData
	 */
	public function edit(TopProductData $topProductData) {
		$this->product = $topProductData->product;
	}

}
