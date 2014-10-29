<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_domains")
 * @ORM\Entity
 */
class ProductDomain {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 */
	private $product;

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $show;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $domainId
	 * @param boolean $show
	 */
	public function __construct(Product $product, $domainId) {
		$this->product = $product;
		$this->domainId = $domainId;
		$this->show = true;
		$this->visible = false;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return boolean
	 */
	public function isShow() {
		return $this->show;
	}

	/**
	 * @param boolean $show
	 */
	public function setShow($show) {
		$this->show = $show;
	}

	/**
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible;
	}

}
