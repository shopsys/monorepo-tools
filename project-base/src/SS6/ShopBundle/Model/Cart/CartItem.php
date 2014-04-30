<?php

namespace SS6\ShopBundle\Model\Cart;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="cart_items")
 * @ORM\Entity
 */
class CartItem {

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=127)
	 */
	private $sessionId;
	
	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 * 
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
	 */
	private $product;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 */
	private $quantity;
	
	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $quantity
	 */
	public function __construct(CustomerIdentifier $customerIdentifier, Product $product, $quantity) {
		$this->sessionId = $customerIdentifier->getSessionId();
		$this->product = $product;
		$this->changeQuantity($quantity);
	}

	/**
	 * @param int $newQuantity
	 */
	public function changeQuantity($newQuantity) {
		if (!is_int($newQuantity) || $newQuantity <= 0) {
			throw new \SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException($newQuantity);
		}

		$this->quantity = $newQuantity;
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
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getName() {
		return $this->product->getName();
	}

	/**
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->product->getPrice();
	}

	/**
	 * @return string
	 */
	public function getTotalPrice() {
		return $this->product->getPrice() * $this->quantity;
	}
}
