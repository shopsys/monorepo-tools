<?php

namespace SS6\ShopBundle\Model\Order\Item;

class OrderItemData {

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $priceWithVat;

	/**
	 * @var string
	 */
	private $priceWithoutVat;

	/**
	 * @var string
	 */
	private $vatPercent;

	/**
	 * @var int
	 */
	private $quantity;

	/**
	 *
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
	public function getPriceWithVat() {
		return $this->priceWithVat;
	}

	/**
	 * @return string
	 */
	public function getPriceWithoutVat() {
		return $this->priceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getVatPercent() {
		return $this->vatPercent;
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param string $priceWithVat
	 */
	public function setPriceWithVat($priceWithVat) {
		$this->priceWithVat = $priceWithVat;
	}

	/**
	 * @param string $priceWithoutVat
	 */
	public function setPriceWithoutVat($priceWithoutVat) {
		$this->priceWithoutVat = $priceWithoutVat;
	}

	/**
	 * @param string $vatPercent
	 */
	public function setVatPercent($vatPercent) {
		$this->vatPercent = $vatPercent;
	}

	/**
	 * @param string $quantity
	 */
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Item\OrderItem $orderItem
	 */
	public function setFromEntity(OrderItem $orderItem) {
		$this->setId($orderItem->getId());
		$this->setName($orderItem->getName());
		$this->setPriceWithVat($orderItem->getPriceWithVat());
		$this->setPriceWithoutVat($orderItem->getPriceWithoutVat());
		$this->setVatPercent($orderItem->getVatPercent());
		$this->setQuantity($orderItem->getQuantity());
	}

}
