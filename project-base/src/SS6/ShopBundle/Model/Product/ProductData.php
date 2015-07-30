<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Brand\Brand;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Product\Product")
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ProductData {

	/**
	 * @var array
	 */
	public $name;

	/**
	 * @var string|null
	 */
	public $catnum;

	/**
	 * @var string|null
	 */
	public $partno;

	/**
	 * @var string|null
	 */
	public $ean;

	/**
	 * @var string
	 */
	public $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat|null
	 */
	public $vat;

	/**
	 * @var \DateTime|null
	 */
	public $sellingFrom;

	/**
	 * @var \DateTime|null
	 */
	public $sellingTo;

	/**
	 * @var bool|null
	 */
	public $sellingDenied;

	/**
	 * @var bool
	 */
	public $hidden;

	/**
	 * @var bool
	 */
	public $usingStock;

	/**
	 * @var int|null
	 */
	public $stockQuantity;

	/**
	 * @var string
	 */
	public $outOfStockAction;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public $availability;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public $outOfStockAvailability;

	/**
	 * @var array
	 */
	public $flags;

	/**
	 * @var array
	 */
	public $hiddenOnDomains;

	/**
	 * @var array
	 */
	public $categories;

	/**
	 * @var string
	 */
	public $priceCalculationType;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand|null
	 */
	public $brand;

	/**
	 * @var string[]
	 */
	public $variantAlias;

	/**
	 * @param array $name
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param string $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param bool $sellingDenied
	 * @param bool $hidden
	 * @param array $flags
	 * @param bool $usingStock
	 * @param string|null $stockQuantity
	 * @param string $outOfStockAction
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability|null $availability
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability|null $outOfStockAvailability
	 * @param array $hiddenOnDomains
	 * @param array $categories
	 * @param string $priceCalculationType
	 * @param \SS6\ShopBundle\Model\Product\Brand\Brand|null $brand
	 * @param string[] $variantAlias
	 */
	public function __construct(
		$name = [],
		$catnum = null,
		$partno = null,
		$ean = null,
		$price = null,
		Vat $vat = null,
		DateTime $sellingFrom = null,
		DateTime $sellingTo = null,
		$sellingDenied = false,
		$hidden = false,
		array $flags = [],
		$usingStock = false,
		$stockQuantity = null,
		$outOfStockAction = null,
		Availability $availability = null,
		Availability $outOfStockAvailability = null,
		array $hiddenOnDomains = [],
		array $categories = [],
		$priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO,
		Brand $brand = null,
		array $variantAlias = []
	) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->price = Condition::ifNull($price, 0);
		$this->vat = $vat;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->sellingDenied = $sellingDenied;
		$this->hidden = $hidden;
		$this->flags = $flags;
		$this->usingStock = $usingStock;
		$this->stockQuantity = $stockQuantity;
		$this->outOfStockAction = $outOfStockAction;
		$this->availability = $availability;
		$this->outOfStockAvailability = $outOfStockAvailability;
		$this->hiddenOnDomains = $hiddenOnDomains;
		$this->categories = $categories;
		$this->priceCalculationType = $priceCalculationType;
		$this->brand = $brand;
		$this->variantAlias = $variantAlias;
	}

}
