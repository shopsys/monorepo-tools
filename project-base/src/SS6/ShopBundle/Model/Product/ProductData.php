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
	 * @var int|null
	 */
	public $stockQuantity;

	/**
	 * @var bool
	 */
	public $hidden;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Availability\Availability|null
	 */
	public $availability;

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
	 * @var int
	 */
	public $priceCalculationType;

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
	 * @param string|null $stockQuantity
	 * @param bool $hidden
	 * @param \SS6\ShopBundle\Model\Availability\Availability|null $availability
	 * @param array $flags
	 * @param array $hiddenOnDomains
	 * @param array $categories
	 * @param int $priceCalculationType
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
		$stockQuantity = null,
		$hidden = false,
		$availability = null,
		array $flags = [],
		array $hiddenOnDomains = [],
		array $categories = [],
		$priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO
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
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
		$this->availability = $availability;
		$this->flags = $flags;
		$this->hiddenOnDomains = $hiddenOnDomains;
		$this->categories = $categories;
		$this->priceCalculationType = $priceCalculationType;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\ProductDomain[] $productDomains
	 */
	public function setFromEntity(Product $product, array $productDomains) {
		$translations = $product->getTranslations();
		$names = [];
		$descriptions = [];
		foreach ($translations as $translation) {
			$names[$translation->getLocale()] = $translation->getName();
			$descriptions[$translation->getLocale()] = $translation->getDescription();
		}
		$this->name = $names;
		$this->description = $descriptions;

		$this->catnum = $product->getCatnum();
		$this->partno = $product->getPartno();
		$this->ean = $product->getEan();
		$this->price = $product->getPrice();
		$this->vat = $product->getVat();
		$this->sellingFrom = $product->getSellingFrom();
		$this->sellingTo = $product->getSellingTo();
		$this->stockQuantity = $product->getStockQuantity();
		$this->availability = $product->getAvailability();
		$this->flags = $product->getFlags()->toArray();
		$this->hidden = $product->isHidden();
		$hiddenOnDomains = [];
		foreach ($productDomains as $productDomain) {
			if ($productDomain->isHidden()) {
				$hiddenOnDomains[] = $productDomain->getDomainId();
			}
		}
		$this->hiddenOnDomains = $hiddenOnDomains;
		$this->categories = $product->getCategories()->toArray();
		$this->priceCalculationType = $product->getPriceCalculationType();
	}

}
