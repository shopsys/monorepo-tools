<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $catnum;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $partno;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $ean;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
	 */
	private $price;
	
	/**
	 * @var DateTime
	 * 
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingFrom;
	
	/**
	 * @var DateTime
	 * 
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingTo;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 * @Assert\Type(type="integer")
	 * @Assert\GreaterThanOrEqual(value=0)
	 */
	private $stockQuantity;
	
	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;
	
	public function __construct() {
		$this->hidden = false;
	}
	
	/**
	 * @return integer 
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return string|null
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string|null $catnum
	 */
	public function setCatnum($catnum) {
		$this->catnum = $catnum;
	}

	/**
	 * @return string|null
	 */
	public function getCatnum() {
		return $this->catnum;
	}

	/**
	 * @param string|null $partno
	 */
	public function setPartno($partno) {
		$this->partno = $partno;
	}

	/**
	 * @return string|null
	 */
	public function getPartno() {
		return $this->partno;
	}

	/**
	 * @param string|null $ean
	 */
	public function setEan($ean) {
		$this->ean = $ean;
	}

	/**
	 * @return string|null
	 */
	public function getEan() {
		return $this->ean;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @return string|null
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @param string|null $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

	/**
	 * @return string|null
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @param DateTime|null $sellingFrom
	 */
	public function setSellingFrom($sellingFrom) {
		$this->sellingFrom = $sellingFrom;
	}
	
	/**
	 * @return DateTime|null
	 */
	public function getSellingFrom() {
		return $this->sellingFrom;
	}
	
	/**
	 * @param DateTime|null $sellingTo
	 */
	public function setSellingTo($sellingTo) {
		$this->sellingTo = $sellingTo;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingTo() {
		return $this->sellingTo;
	}
	
	/**
	 * @param int|null $stockQuantity
	 */
	public function setStockQuantity($stockQuantity) {
		$this->stockQuantity = $stockQuantity;
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
	 * @param boolean $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}
	
	/**
	 * @return boolean
	 */
	public function isVisible() {
		$datetime = new DateTime();
		
		if ($this->isHidden()) {
			return false;
		}
		
		if ($this->getSellingFrom() instanceof DateTime) {
			if ($this->getSellingFrom() > $datetime) {
				return false;
			}
		}
		
		if ($this->getSellingTo() instanceof DateTime) {
			if ($this->getSellingTo() < $datetime) {
				return false;
			}
		}
		
		return true;
	}

}
