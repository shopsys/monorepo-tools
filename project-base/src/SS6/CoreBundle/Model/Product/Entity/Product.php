<?php

namespace SS6\CoreBundle\Model\Product\Entity;

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
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="text")
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="catnum", type="string", length=100)
	 */
	private $catnum;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="partno", type="string", length=100)
	 */
	private $partno;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ean", type="string", length=100)
	 */
	private $ean;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text")
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="price", type="decimal")
	 */
	private $price;

	/**
	 * Get id
	 *
	 * @return integer 
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return Product
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string 
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set catnum
	 *
	 * @param string $catnum
	 * @return Product
	 */
	public function setCatnum($catnum) {
		$this->catnum = $catnum;

		return $this;
	}

	/**
	 * Get catnum
	 *
	 * @return string 
	 */
	public function getCatnum() {
		return $this->catnum;
	}

	/**
	 * Set partno
	 *
	 * @param string $partno
	 * @return Product
	 */
	public function setPartno($partno) {
		$this->partno = $partno;

		return $this;
	}

	/**
	 * Get partno
	 *
	 * @return string 
	 */
	public function getPartno() {
		return $this->partno;
	}

	/**
	 * Set ean
	 *
	 * @param string $ean
	 * @return Product
	 */
	public function setEan($ean) {
		$this->ean = $ean;

		return $this;
	}

	/**
	 * Get ean
	 *
	 * @return string 
	 */
	public function getEan() {
		return $this->ean;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return Product
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string 
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set price
	 *
	 * @param string $price
	 * @return Product
	 */
	public function setPrice($price) {
		$this->price = $price;

		return $this;
	}

	/**
	 * Get price
	 *
	 * @return string 
	 */
	public function getPrice() {
		return $this->price;
	}

}
