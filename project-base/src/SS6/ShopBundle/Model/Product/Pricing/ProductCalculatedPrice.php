<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_calculated_prices")
 * @ORM\Entity
 */
class ProductCalculatedPrice {

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
	 * @var string|null
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
	 */
	private $priceWithVat;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string|null $priceWithVat
	 */
	public function __construct(Product $product, PricingGroup $pricingGroup, $priceWithVat) {
		$this->product = $product;
		$this->pricingGroup = $pricingGroup;
		$this->priceWithVat = $priceWithVat;
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
	 * @param string|null $priceWithVat
	 */
	public function setPriceWithVat($priceWithVat) {
		$this->priceWithVat = $priceWithVat;
	}

}
