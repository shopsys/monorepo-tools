<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

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
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Vat\Vat")
	 */
	private $vat;

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
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="availability_id", referencedColumnName="id", nullable=true)
	 */
	private $availability;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function __construct(ProductData $productData) {
		$this->name = $productData->getName();
		$this->catnum = $productData->getCatnum();
		$this->partno = $productData->getPartno();
		$this->ean = $productData->getEan();
		$this->description = $productData->getDescription();
		$this->setPrice($productData->getPrice());
		$this->vat = $productData->getVat();
		$this->sellingFrom = $productData->getSellingFrom();
		$this->sellingTo = $productData->getSellingTo();
		$this->stockQuantity = $productData->getStockQuantity();
		$this->hidden = $productData->isHidden();
		$this->availability = $productData->getAvailability();
		$this->visible = false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function edit(ProductData $productData) {
		$this->name = $productData->getName();
		$this->catnum = $productData->getCatnum();
		$this->partno = $productData->getPartno();
		$this->ean = $productData->getEan();
		$this->description = $productData->getDescription();
		$this->setPrice($productData->getPrice());
		$this->vat = $productData->getVat();
		$this->sellingFrom = $productData->getSellingFrom();
		$this->sellingTo = $productData->getSellingTo();
		$this->stockQuantity = $productData->getStockQuantity();
		$this->availability = $productData->getAvailability();
		$this->hidden = $productData->isHidden();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Vat $vat) {
		$this->vat = $vat;
	}

	/**
	 * @param string|null $price
	 */
	public function setPrice($price) {
		$this->price = Condition::ifNull($price, 0);
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
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
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
	 *
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible;
	}

}
