<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Component\Validator;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use DateTime;

/**
 * @Validator\Auto(entity="SS6\ShopBundle\Model\Product\Product")
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ProductData {

	/**
	 * @var array
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
	 * @var array
	 */
	private $description;

	/**
	 * @var string
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat|null
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
	 * @var bool
	 */
	private $hidden;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Availability\Availability|null
	 */
	private $availability;

	/**
	 * @var array
	 */
	private $hiddenOnDomains;

	/**
	 * @var array
	 */
	private $categories;

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
	 * @param array $hiddenOnDomains
	 * @param array $categories
	 */
	public function __construct(
		$name = array(),
		$catnum = null,
		$partno = null,
		$ean = null,
		$description = array(),
		$price = null,
		Vat $vat = null,
		DateTime $sellingFrom = null,
		DateTime $sellingTo = null,
		$stockQuantity = null,
		$hidden = false,
		$availability = null,
		array $hiddenOnDomains = array(),
		array $categories = array()
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
		$this->hiddenOnDomains = $hiddenOnDomains;
		$this->categories = $categories;
	}

	/**
	 * @return array
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
	 * @return array
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat|null
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
	 * @return \SS6\ShopBundle\Model\Availability\Availability|null
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * @return array
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @param array $name
	 */
	public function setName(array $name) {
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
	 * @param array $description
	 */
	public function setDescription(array $description) {
		$this->description = $description;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
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
	 * @param \SS6\ShopBundle\Model\Availability\Availability|null $availability
	 */
	public function setAvailability(Availability $availability = null) {
		$this->availability = $availability;
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @return array
	 */
	public function getHiddenOnDomains() {
		return $this->hiddenOnDomains;
	}

	/**
	 * @param bool $hidden
	 */
	public function setHidden($hidden) {
		$this->hidden = $hidden;
	}

	/**
	 * @param array $domains
	 */
	public function setHiddenOnDomains(array $domains) {
		$this->hiddenOnDomains = $domains;
	}

	/**
	 * @param array $categories
	 */
	public function setCategories(array $categories) {
		$this->categories = $categories;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Product\ProductDomain[] $productDomains
	 */
	public function setFromEntity(Product $product, array $productDomains) {
		$translations = $product->getTranslations();
		$names = array();
		$desctiptions = array();
		foreach ($translations as $translation) {
			$names[$translation->getLocale()] = $translation->getName();
			$desctiptions[$translation->getLocale()] = $translation->getDescription();
		}
		$this->setName($names);
		$this->setDescription($desctiptions);

		$this->setCatnum($product->getCatnum());
		$this->setPartno($product->getPartno());
		$this->setEan($product->getEan());
		$this->setPrice($product->getPrice());
		$this->setVat($product->getVat());
		$this->setSellingFrom($product->getSellingFrom());
		$this->setSellingTo($product->getSellingTo());
		$this->setStockQuantity($product->getStockQuantity());
		$this->setAvailability($product->getAvailability());
		$this->setHidden($product->isHidden());
		$hiddenOnDomains = array();
		foreach ($productDomains as $productDomain) {
			if ($productDomain->isHidden()) {
				$hiddenOnDomains[] = $productDomain->getDomainId();
			}
		}
		$this->setHiddenOnDomains($hiddenOnDomains);
		$this->setCategories($product->getCategories()->toArray());
	}

}
