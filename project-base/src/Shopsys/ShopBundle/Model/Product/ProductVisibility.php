<?php

namespace SS6\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_visibilities")
 * @ORM\Entity
 */
class ProductVisibility {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product")
	 * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $product;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 *
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Group\PricingGroup")
	 * @ORM\JoinColumn(nullable=false, name="pricing_group_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $pricingGroup;

	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int $domainId
	 */
	public function __construct(
		Product $product,
		PricingGroup $pricingGroup,
		$domainId
	) {
		$this->product = $product;
		$this->pricingGroup = $pricingGroup;
		$this->domainId = $domainId;
		$this->visible = false;
	}

	public function isVisible() {
		return $this->visible;
	}

}
