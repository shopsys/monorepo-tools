<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Availability\Availability;

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
	 * @var array
	 */
	public $description;

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
	public $sellable;

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
	 *
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public $availability;

	/**
	 *
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
	 * @var \SS6\ShopBundle\Model\Product\Product[]
	 */
	public $accessories;

	/**
	 * @param array $name
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param array $description
	 * @param string $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param bool $sellable
	 * @param bool $hidden
	 * @param array $flags
	 * @param bool $usingStock
	 * @param string|null $stockQuantity
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability|null $availability
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability|null $outOfStockAvailability
	 * @param array $hiddenOnDomains
	 * @param array $categories
	 * @param string $priceCalculationType
	 */
	public function __construct(
		$name = [],
		$catnum = null,
		$partno = null,
		$ean = null,
		$description = [],
		$price = null,
		Vat $vat = null,
		DateTime $sellingFrom = null,
		DateTime $sellingTo = null,
		$sellable = true,
		$hidden = false,
		array $flags = [],
		$usingStock = false,
		$stockQuantity = null,
		Availability $availability = null,
		Availability $outOfStockAvailability = null,
		array $hiddenOnDomains = [],
		array $categories = [],
		$priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO,
		array $accessories = []
	) {
		$this->name = $name;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->description = $description;
		$this->price = Condition::ifNull($price, 0);
		$this->vat = $vat;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->sellable = $sellable;
		$this->hidden = $hidden;
		$this->flags = $flags;
		$this->usingStock = $usingStock;
		$this->stockQuantity = $stockQuantity;
		$this->availability = $availability;
		$this->outOfStockAvailability = $outOfStockAvailability;
		$this->hiddenOnDomains = $hiddenOnDomains;
		$this->categories = $categories;
		$this->priceCalculationType = $priceCalculationType;
		$this->accessories = $accessories;
	}

}
