<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use DateTime;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class ProductData {

	/**
	 * @var array
	 */
	private $names;

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
	private $descriptions;

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
	 * @var string|null
	 */
	private $image;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Availability\Availability|null
	 */
	private $availability;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	private $parameters;

	/**
	 * @var array
	 */
	private $hiddenOnDomains;

	/**
	 * @param array $names
	 * @param string|null $catnum
	 * @param string|null $partno
	 * @param string|null $ean
	 * @param array $descriptions
	 * @param string $price
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat|null $vat
	 * @param \DateTime|null $sellingFrom
	 * @param \DateTime|null $sellingTo
	 * @param string|null $stockQuantity
	 * @param bool $hidden
	 * @param string|null $image
	 * @param \SS6\ShopBundle\Model\Availability\Availability|null $availability
	 * @param array $parameters
	 * @param array $hiddenOnDomains
	 */
	public function __construct(
		$names = array(),
		$catnum = null,
		$partno = null,
		$ean = null,
		$descriptions = array(),
		$price = null,
		Vat $vat = null,
		DateTime $sellingFrom = null,
		DateTime $sellingTo = null,
		$stockQuantity = null,
		$hidden = false,
		$image = null,
		$availability = null,
		array $parameters = array(),
		array $hiddenOnDomains = array()
	) {
		$this->names = $names;
		$this->catnum = $catnum;
		$this->partno = $partno;
		$this->ean = $ean;
		$this->descriptions = $descriptions;
		$this->price = Condition::ifNull($price, 0);
		$this->vat = $vat;
		$this->sellingFrom = $sellingFrom;
		$this->sellingTo = $sellingTo;
		$this->stockQuantity = $stockQuantity;
		$this->hidden = $hidden;
		$this->image = $image;
		$this->availability = $availability;
		$this->parameters = $parameters;
		$this->hiddenOnDomains = $hiddenOnDomains;
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
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
	public function getDescriptions() {
		return $this->descriptions;
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
	 * @return string|null
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Availability\Availability|null
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[]
	 */
	public function getParameters() {
		return $this->parameters;
	}

	/**
	 * @param array $names
	 */
	public function setNames(array $names) {
		$this->names = $names;
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
	 * @param array $descriptions
	 */
	public function setDescriptions(array $descriptions) {
		$this->descriptions = $descriptions;
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
	 * @param string|null $image
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Availability\Availability|null $availability
	 */
	public function setAvailability(Availability $availability = null) {
		$this->availability = $availability;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValueData[] $parameters
	 */
	public function setParameters($parameters) {
		$this->parameters = $parameters;
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
		$this->setNames($names);
		$this->setDescriptions($desctiptions);

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
	}

}
