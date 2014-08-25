<?php

namespace SS6\ShopBundle\Model\Payment;

use Doctrine\Common\Collections\Collection;
use SS6\ShopBundle\Model\Pricing\Vat;

class PaymentData {
	
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat
	 */
	private $vat;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var integer
	 */
	private $hidden;

	/**
	 * @var string
	 */
	private $image;
	
	/**
	 * @var \Doctrine\Common\Collections\Collection 
	 */
	private $transports;

	/**
	 * @param string|null $name
	 * @param string|null $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat|null $vat
	 * @param string|null $description
	 * @param boolean $hidden
	 */
	public function __construct(
		$name = null,
		$price = null,
		Vat $vat = null,
		$description = null,
		$hidden = false
	) {
		$this->name = $name;
		$this->price = $price;
		$this->vat = $vat;
		$this->description = $description;
		$this->hidden = $hidden;
		$this->transports = array();
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTransports() {
		return $this->transports;
	}

	/**
	 * @return string
	 */
	public function getImage() {
		return $this->image;
	}
	
	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $price
	 */
	public function setPrice($price) {
		$this->price = $price;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat $vat
	 */
	public function setVat(Vat $vat = null) {
		$this->vat = $vat;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}
	
	/**
	 * @param Collection $transports
	 */
	public function setTransports($transports) {
		$this->transports = $transports;
	}
	
	/**
	 * @param boolean $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @param string $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}
}
