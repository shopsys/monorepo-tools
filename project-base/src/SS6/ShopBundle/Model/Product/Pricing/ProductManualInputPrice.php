<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_manual_input_prices")
 * @ORM\Entity
 */
class ProductManualInputPrice {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Group\PricingGroup")
	 * @ORM\JoinColumn(nullable=false, name="pricing_group_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $pricingGroup;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
	 */
	private $inputPrice;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $inputPrice
	 */
	public function __construct(Product $product, PricingGroup $pricingGroup, $inputPrice) {
		$this->product = $product;
		$this->pricingGroup = $pricingGroup;
		$this->inputPrice = $inputPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProduct() {
		return $this->product;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getPricingGroup() {
		return $this->pricingGroup;
	}

	/**
	 * @return string
	 */
	public function getInputPrice() {
		return $this->inputPrice;
	}

	/**
	 * @param string $inputPrice
	 */
	public function setInputPrice($inputPrice) {
		$this->inputPrice = $inputPrice;
	}

}
