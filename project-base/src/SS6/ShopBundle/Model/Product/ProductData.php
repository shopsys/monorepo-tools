<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Pricing\Vat;
use DateTime;

class ProductData {

	/**
	 * @var string|null
	 */
	private $name;

	/**
	 * @var string|null
	 */
	private $catnum;

	/**
	 * @var string|null
	 */
	private $partno;

	/**
	 * @var string|null
	 */
	private $ean;

	/**
	 * @var string|null
	 */
	private $description;

	/**
	 * @var string|null
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat|null
	 */
	private $vat;

	/**
	 * @var \DateTime|null
	 */
	private $sellingFrom;

	/**
	 * @var \DateTime|null
	 */
	private $sellingTo;

	/**
	 * @var int|null
	 */
	private $stockQuantity;

	/**
	 * @var boolean
	 */
	private $hidden;

	/**
	 * @var string|null
	 */
	private $image;

	/**
	 * @param string|null $name
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param string|null $description
	 * @param string|null $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat|null $vat
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param string|null $stockQuantity
	 * @param string|null $hidden
	 * @param string|null $image
	 */
	public function __construct(
		$name = null,
		$catnum = null,
		$partno = null,
		$ean = null,
		$description = null,
		$price = null,
		Vat $vat = null,
		DateTime $sellingFrom = null,
		DateTime $sellingTo = null,
		$stockQuantity = null,
		$hidden = false,
		$image = null
	) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->description = $description;
		$this->price = $price;
		$this->vat = $vat;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
		$this->image = $image;
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
	 * @return \SS6\ShopBundle\Model\Pricing\Vat|null
	 */
	public function getVat() {
		return $this->vat;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getSellingFrom() {
		return $this->sellingFrom;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getSellingTo() {
		return $this->sellingTo;
	}

	/**
	 * @return string|null
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
	}

	/**
	 * @return boolean
	 */
	public function getHidden() {
		return $this->hidden;
	}

	/**
	 * @return string|null
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @param string|null $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string|null $catnum
	 */
	public function setCatnum($catnum) {
		$this->catnum = $catnum;
	}

	/**
	 * @param string|null $partno
	 */
	public function setPartno($partno) {
		$this->partno = $partno;
	}

	/**
	 * @param string|null $ean
	 */
	public function setEan($ean) {
		$this->ean = $ean;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat|null $vat
	 */
	public function setVat(Vat $vat = null) {
		$this->vat = $vat;
	}

	/**
	 * @param string|null $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

	/**
	 * @param \DateTime|null $sellingFrom
	 */
	public function setSellingFrom(DateTime $sellingFrom = null) {
		$this->sellingFrom = $sellingFrom;
	}

	/**
	 * @param \DateTime|null $sellingTo
	 */
	public function setSellingTo(DateTime $sellingTo = null) {
		$this->sellingTo = $sellingTo;
	}

	/**
	 * @param string|null $stockQuantity
	 */
	public function setStockQuantity($stockQuantity) {
		$this->stockQuantity = $stockQuantity;
	}

	/**
	 * @param boolean $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @param string|null $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function setFromEntity(Product $product) {
		$this->setName($product->getName());
		$this->setCatnum($product->getCatnum());
		$this->setPartno($product->getPartno());
		$this->setEan($product->getEan());
		$this->setDescription($product->getDescription());
		$this->setPrice($product->getPrice());
		$this->setVat($product->getVat());
		$this->setSellingFrom($product->getSellingFrom());
		$this->setSellingTo($product->getSellingTo());
		$this->setStockQuantity($product->getStockQuantity());
		$this->setHidden($product->isHidden());
	}

}
