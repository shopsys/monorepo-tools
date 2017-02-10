<?php

namespace Shopsys\ShopBundle\Model\Product\BestsellingProduct;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(
 *	name="products_manual_bestselling",
 *	uniqueConstraints={
 *		@ORM\UniqueConstraint(columns={"product_id", "category_id", "domain_id"}),
 *		@ORM\UniqueConstraint(columns={"position", "category_id", "domain_id"})
 *	}
 * )
 * @ORM\Entity
 */
class ManualBestsellingProduct {

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Product
	 *
	 * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var int
	 *
	 * @ORM\ManyToOne(targetEntity="Shopsys\ShopBundle\Model\Category\Category", inversedBy="domains")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 */
	private $category;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $position;

	/**
	 * @param int $domainId
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @param \Shopsys\ShopBundle\Model\Product\Product $product
	 * @param int $position
	 */
	public function __construct($domainId, Category $category, Product $product, $position) {
		$this->product = $product;
		$this->category = $category;
		$this->domainId = $domainId;
		$this->position = $position;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Category\Category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return int
	 */
	public function getPosition() {
		return $this->position;
	}

}
