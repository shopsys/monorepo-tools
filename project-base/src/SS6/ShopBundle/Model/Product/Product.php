<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Product extends AbstractTranslatableEntity {

	const PRICE_CALCULATION_TYPE_AUTO = 1;
	const PRICE_CALCULATION_TYPE_MANUAL = 2;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductTranslation[]
	 *
	 * @Prezent\Translations(targetEntity="SS6\ShopBundle\Model\Product\ProductTranslation")
	 */
	protected $translations;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $catnum;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $partno;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $ean;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="decimal", precision=20, scale=6)
	 */
	private $price;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Vat\Vat")
	 */
	private $vat;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingFrom;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $sellingTo;

	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $stockQuantity;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="availability_id", referencedColumnName="id", nullable=true)
	 */
	private $availability;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $visible;

	/**
	 * @var \SS6\ShopBundle\Model\Category\Category[]
	 *
	 * @ORM\ManyToMany(targetEntity="SS6\ShopBundle\Model\Category\Category")
	 * @ORM\JoinTable(name="product_categories")
	 */
	private $categories;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $priceCalculationType;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function __construct(ProductData $productData) {
		$this->translations = new ArrayCollection();
		$this->catnum = $productData->catnum;
		$this->partno = $productData->partno;
		$this->ean = $productData->ean;
		$this->priceCalculationType = $productData->priceCalculationType;
		if ($this->getPriceCalculationType() === self::PRICE_CALCULATION_TYPE_AUTO) {
			$this->setPrice($productData->price);
		} else {
			$this->setPrice(null);
		}
		$this->vat = $productData->vat;
		$this->sellingFrom = $productData->sellingFrom;
		$this->sellingTo = $productData->sellingTo;
		$this->stockQuantity = $productData->stockQuantity;
		$this->hidden = $productData->hidden;
		$this->availability = $productData->availability;
		$this->visible = false;
		$this->setTranslations($productData);
		$this->categories = $productData->categories;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function edit(ProductData $productData) {
		$this->catnum = $productData->catnum;
		$this->partno = $productData->partno;
		$this->ean = $productData->ean;
		$this->priceCalculationType = $productData->priceCalculationType;
		if ($this->getPriceCalculationType() === self::PRICE_CALCULATION_TYPE_AUTO) {
			$this->setPrice($productData->price);
		} else {
			$this->setPrice(null);
		}
		$this->vat = $productData->vat;
		$this->sellingFrom = $productData->sellingFrom;
		$this->sellingTo = $productData->sellingTo;
		$this->stockQuantity = $productData->stockQuantity;
		$this->availability = $productData->availability;
		$this->hidden = $productData->hidden;
		$this->setTranslations($productData);
		$this->categories = $productData->categories;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Vat $vat) {
		$this->vat = $vat;
	}

	/**
	 * @param string|null $price
	 */
	public function setPrice($price) {
		$this->price = Condition::ifNull($price, 0);
	}

	/**
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getName($locale = null) {
		return $this->translation($locale)->getName();
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
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getDescription($locale = null) {
		return $this->translation($locale)->getDescription();
	}

	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Vat\Vat
	 */
	public function getVat() {
		return $this->vat;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingFrom() {
		return $this->sellingFrom;
	}

	/**
	 * @return DateTime|null
	 */
	public function getSellingTo() {
		return $this->sellingTo;
	}

	/**
	 * @return int|null
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
	}

	/**
	 * @return boolean
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 *
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * @return int
	 */
	public function getPriceCalculationType() {
		return $this->priceCalculationType;
	}

	/**
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 */
	private function setTranslations(ProductData $productData) {
		foreach ($productData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
		foreach ($productData->description as $locale => $description) {
			$this->translation($locale)->setDescription($description);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductTranslation
	 */
	protected function createTranslation() {
		return new ProductTranslation();
	}

}
