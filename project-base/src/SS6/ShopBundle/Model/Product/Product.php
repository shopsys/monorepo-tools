<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Product {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 */
	private $name;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $catnum;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $partno;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $ean;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
	 */
	private $price;
	
	/**
	 * @var \DateTime|null
	 * 
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingFrom;
	
	/**
	 * @var \DateTime|null
	 * 
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingTo;
	
	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $stockQuantity;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;
	
	/**
	 * @param string $name
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param string|null $description
	 * @param string|null $price
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param int|null $stockQuantity
	 * @param boolean $hidden
	 */
	public function __construct($name, $catnum = null, $partno = null, $ean = null,
			$description = null, $price = null, $sellingFrom = null, $sellingTo = null,
			$stockQuantity = null, $hidden = false) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->description = $description;
		$this->price = $price;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
		$this->visible = false;
	}
	
	/**
	 * @param string $name
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param string|null $description
	 * @param string|null $price
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param int|null $stockQuantity
	 * @param boolean $hidden
	 */
	public function edit($name, $catnum, $partno, $ean, $description, $price, $sellingFrom,
			$sellingTo, $stockQuantity, $hidden) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->description = $description;
		$this->price = $price;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
	}

	/**
	 * @return integer 
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getCatnum() {
		return $this->catnum;
	}

	/**
	 * @return string|null
	 */
	public function getPartno() {
		return $this->partno;
	}

	/**
	 * @return string|null
	 */
	public function getEan() {
		return $this->ean;
	}

	/**
	 * @return string|null
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string|null
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingFrom() {
		return $this->sellingFrom;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingTo() {
		return $this->sellingTo;
	}
	
	/**
	 * @return int|null
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
	}
	
	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}
	
	/**
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible;
	}

}
