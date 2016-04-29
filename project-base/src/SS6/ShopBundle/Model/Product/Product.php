<?php

namespace SS6\ShopBundle\Model\Product;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Localization\AbstractTranslatableEntity;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\ProductCategoryDomain;

/**
 * Product
 *
 * @ORM\Table(
 *		name="products",
 *		indexes={
 *			@ORM\Index(columns={"variant_type"})
 *		}
 *	)
 * @ORM\Entity
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Product extends AbstractTranslatableEntity {

	const PRICE_CALCULATION_TYPE_AUTO = 'auto';
	const PRICE_CALCULATION_TYPE_MANUAL = 'manual';
	const OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY = 'setAlternateAvailability';
	const OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE = 'excludeFromSale';
	const OUT_OF_STOCK_ACTION_HIDE = 'hide';
	const VARIANT_TYPE_NONE = 'none';
	const VARIANT_TYPE_MAIN = 'main';
	const VARIANT_TYPE_VARIANT = 'variant';

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
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
	 * @var string
	 *
	 * @ORM\Column(type="tsvector", nullable=false)
	 */
	private $catnumTsvector;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $partno;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="tsvector", nullable=false)
	 */
	private $partnoTsvector;

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
	 * @ORM\JoinColumn(nullable=false)
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
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $sellingDenied;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $calculatedSellingDenied;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $calculatedHidden;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $usingStock;

	/**
	 * @var int|null
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $stockQuantity;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Unit\Unit
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Unit\Unit")
	 * @ORM\JoinColumn(name="unit_id", referencedColumnName="id", nullable=false)
	 */
	private $unit;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $outOfStockAction;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="availability_id", referencedColumnName="id", nullable=true)
	 */
	private $availability;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="out_of_stock_availability_id", referencedColumnName="id", nullable=true)
	 */
	private $outOfStockAvailability;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Availability\Availability
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Availability\Availability")
	 * @ORM\JoinColumn(name="calculated_availability_id", referencedColumnName="id", nullable=false)
	 */
	private $calculatedAvailability;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", options={"default" = true})
	 */
	private $recalculateAvailability;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean")
	 */
	private $calculatedVisibility;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection|\SS6\ShopBundle\Model\Product\ProductCategoryDomain[]
	 *
	 * @ORM\OneToMany(
	 *   targetEntity="SS6\ShopBundle\Model\Product\ProductCategoryDomain",
	 *   mappedBy="product",
	 *   orphanRemoval=true,
	 *   cascade={"persist"}
	 * )
	 */
	private $productCategoryDomains;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection|\SS6\ShopBundle\Model\Product\Flag\Flag[]
	 *
	 * @ORM\ManyToMany(targetEntity="SS6\ShopBundle\Model\Product\Flag\Flag")
	 * @ORM\JoinTable(name="product_flags")
	 */
	private $flags;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=32)
	 */
	private $priceCalculationType;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", options={"default" = true})
	 */
	private $recalculatePrice;

	/**
	 * @var bool
	 *
	 * @ORM\Column(type="boolean", options={"default" = true})
	 */
	private $recalculateVisibility;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Brand\Brand|null
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Brand\Brand")
	 * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
	 */
	private $brand;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection|\SS6\ShopBundle\Model\Product\Product[]
	 *
	 * @ORM\OneToMany(targetEntity="SS6\ShopBundle\Model\Product\Product", mappedBy="mainVariant", cascade={"persist"})
	 */
	private $variants;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Product|null
	 *
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Product\Product", inversedBy="variants", cascade={"persist"})
	 * @ORM\JoinColumn(name="main_variant_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
	 */
	private $mainVariant;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", length=32, nullable=false)
	 */
	private $variantType;

	/**
	 * @var int
	 *
	 * @ORM\Column(type="integer")
	 */
	private $orderingPriority;

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @param \SS6\ShopBundle\Model\Product\Product[]|null $variants
	 */
	private function __construct(ProductData $productData, array $variants = null) {
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
		$this->sellingDenied = $productData->sellingDenied;
		$this->hidden = $productData->hidden;
		$this->usingStock = $productData->usingStock;
		$this->stockQuantity = $productData->stockQuantity;
		$this->unit = $productData->unit;
		$this->outOfStockAction = $productData->outOfStockAction;
		$this->availability = $productData->availability;
		$this->outOfStockAvailability = $productData->outOfStockAvailability;
		$this->calculatedAvailability = $this->availability;
		$this->recalculateAvailability = true;
		$this->calculatedVisibility = false;
		$this->setTranslations($productData);
		$this->productCategoryDomains = new ArrayCollection();
		$this->flags = new ArrayCollection($productData->flags);
		$this->recalculatePrice = true;
		$this->recalculateVisibility = true;
		$this->calculatedHidden = true;
		$this->calculatedSellingDenied = true;
		$this->brand = $productData->brand;
		$this->orderingPriority = $productData->orderingPriority;

		$this->variants = new ArrayCollection();
		if ($variants === null) {
			$this->variantType = self::VARIANT_TYPE_NONE;
		} else {
			$this->variantType = self::VARIANT_TYPE_MAIN;
			$this->addVariants($variants);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public static function create(ProductData $productData) {
		return new self($productData, null);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 * @param \SS6\ShopBundle\Model\Product\ProductData[] $variants
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public static function createMainVariant(ProductData $productData, array $variants) {
		return new self($productData, $variants);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData
	 */
	public function edit(ProductData $productData) {
		$this->vat = $productData->vat;
		$this->sellingFrom = $productData->sellingFrom;
		$this->sellingTo = $productData->sellingTo;
		$this->sellingDenied = $productData->sellingDenied;
		$this->recalculateAvailability = true;
		$this->hidden = $productData->hidden;
		$this->editFlags($productData->flags);
		$this->brand = $productData->brand;
		$this->unit = $productData->unit;
		$this->setTranslations($productData);

		if (!$this->isVariant()) {
			$this->setCategories($productData->categoriesByDomainId);
		}
		if (!$this->isMainVariant()) {
			$this->usingStock = $productData->usingStock;
			$this->stockQuantity = $productData->stockQuantity;
			$this->outOfStockAction = $productData->outOfStockAction;
			$this->availability = $productData->availability;
			$this->outOfStockAvailability = $productData->outOfStockAvailability;
			$this->catnum = $productData->catnum;
			$this->partno = $productData->partno;
			$this->ean = $productData->ean;
			$this->priceCalculationType = $productData->priceCalculationType;
			if ($this->getPriceCalculationType() === self::PRICE_CALCULATION_TYPE_AUTO) {
				$this->setPrice($productData->price);
			} else {
				$this->setPrice(null);
			}
		}

		$this->orderingPriority = $productData->orderingPriority;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flags
	 */
	private function editFlags(array $flags) {
		$this->flags->clear();
		foreach ($flags as $flag) {
			$this->flags->add($flag);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Vat\Vat $vat
	 */
	public function changeVat(Vat $vat) {
		$this->vat = $vat;
		$this->recalculatePrice = true;
	}

	/**
	 * @param string|null $price
	 */
	public function setPrice($price) {
		$this->price = Condition::ifNull($price, 0);
	}

	/**
	 * @return int
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
	 * @param string|null $locale
	 * @return string|null
	 */
	public function getVariantAlias($locale = null) {
		return $this->translation($locale)->getVariantAlias();
	}

	/**
	 * @return string[locale]
	 */
	public function getNames() {
		$names = [];
		foreach ($this->translations as $translation) {
			$names[$translation->getLocale()] = $translation->getName();
		}

		return $names;
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
	 * @return bool
	 */
	public function isHidden() {
		return $this->hidden;
	}

	/**
	 * @return bool
	 */
	public function getCalculatedHidden() {
		return $this->calculatedHidden;
	}

	/**
	 * @return bool
	 */
	public function isSellingDenied() {
		return $this->sellingDenied;
	}

	/**
	 * @return bool
	 */
	public function getCalculatedSellingDenied() {
		return $this->calculatedSellingDenied;
	}

	/**
	 * @return bool
	 */
	public function isUsingStock() {
		return $this->usingStock;
	}

	/**
	 * @return int|null
	 */
	public function getStockQuantity() {
		return $this->stockQuantity;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Unit\Unit
	 */
	public function getUnit() {
		return $this->unit;
	}

	/**
	 * @return string
	 */
	public function getOutOfStockAction() {
		return $this->outOfStockAction;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability|null
	 */
	public function getOutOfStockAvailability() {
		return $this->outOfStockAvailability;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Availability\Availability
	 */
	public function getCalculatedAvailability() {
		return $this->calculatedAvailability;
	}

	/**
	 * @return int
	 */
	public function getOrderingPriority() {
		return $this->orderingPriority;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $availability
	 */
	public function setAvailability(Availability $availability) {
		$this->availability = $availability;
		$this->recalculateAvailability = true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability|null $outOfStockAvailability
	 */
	public function setOutOfStockAvailability(Availability $outOfStockAvailability = null) {
		$this->outOfStockAvailability = $outOfStockAvailability;
		$this->recalculateAvailability = true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Availability\Availability $calculatedAvailability
	 */
	public function setCalculatedAvailability(Availability $calculatedAvailability) {
		$this->calculatedAvailability = $calculatedAvailability;
		$this->recalculateAvailability = false;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @param int $domainId
	 */
	public function setCategories(array $categoriesByDomainId) {
		foreach ($categoriesByDomainId as $domainId => $categories) {
			$this->removeOldProductCategoryDomains($categories, $domainId);
			$this->createNewProductCategoryDomains($categories, $domainId);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $newCategories
	 * @param int $domainId
	 */
	private function createNewProductCategoryDomains(array $newCategories, $domainId) {
		$currentProductCategoryDomainsOnDomainByCategoryId = $this->getProductCategoryDomainsByDomainIdIndexedByCategoryId($domainId);

		foreach ($newCategories as $newCategory) {
			if (!array_key_exists($newCategory->getId(), $currentProductCategoryDomainsOnDomainByCategoryId)) {
				$productCategoryDomain = new ProductCategoryDomain($this, $newCategory, $domainId);
				$this->productCategoryDomains->add($productCategoryDomain);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $newCategories
	 * @param int $domainId
	 */
	private function removeOldProductCategoryDomains(array $newCategories, $domainId) {
		$currentProductCategoryDomains = $this->getProductCategoryDomainsByDomainIdIndexedByCategoryId($domainId);

		foreach ($currentProductCategoryDomains as $currentProductCategoryDomain) {
			if (!in_array($currentProductCategoryDomain->getCategory(), $newCategories, true)) {
				$this->productCategoryDomains->removeElement($currentProductCategoryDomain);
			}
		}
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Product\ProductCategoryDomain[categoryId]
	 */
	private function getProductCategoryDomainsByDomainIdIndexedByCategoryId($domainId) {
		$productCategoryDomainsByCategoryId = [];

		foreach ($this->productCategoryDomains as $productCategoryDomain) {
			if ($productCategoryDomain->getDomainId() === $domainId) {
				$productCategoryDomainsByCategoryId[$productCategoryDomain->getCategory()->getId()] = $productCategoryDomain;
			}
		}

		return $productCategoryDomainsByCategoryId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Flag\Flag[]
	 */
	public function getFlags() {
		return $this->flags;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\Category[domainId][]
	 */
	public function getCategoriesIndexedByDomainId() {
		$categoriesByDomainId = [];

		foreach ($this->productCategoryDomains as $productCategoryDomain) {
			$categoriesByDomainId[$productCategoryDomain->getDomainId()][] = $productCategoryDomain->getCategory();
		}

		return $categoriesByDomainId;
	}

	/**
	 * @return string
	 */
	public function getPriceCalculationType() {
		return $this->priceCalculationType;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Brand\Brand
	 */
	public function getBrand() {
		return $this->brand;
	}

	/**
	 * @return bool
	 */
	private function getCalculatedVisibility() {
		return $this->calculatedVisibility;
	}

	/**
	 * @return bool
	 */
	public function isVisible() {
		return $this->getCalculatedVisibility();
	}

	public function markPriceAsRecalculated() {
		$this->recalculatePrice = false;
	}

	public function markForVisibilityRecalculation() {
		$this->recalculateVisibility = true;
	}

	public function markForAvailabilityRecalculation() {
		$this->recalculateAvailability = true;
	}

	/**
	 * @return bool
	 */
	public function isMainVariant() {
		return $this->variantType === self::VARIANT_TYPE_MAIN;
	}

	/**
	 * @return bool
	 */
	public function isVariant() {
		return $this->variantType === self::VARIANT_TYPE_VARIANT;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getMainVariant() {
		if (!$this->isVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductIsNotVariantException();
		}

		return $this->mainVariant;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $variant
	 */
	public function addVariant(Product $variant) {
		if (!$this->isMainVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\VariantCanBeAddedOnlyToMainVariantException(
				$this->getId(),
				$variant->getId()
			);
		}
		if ($variant->isMainVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\MainVariantCannotBeVariantException($variant->getId());
		}
		if ($variant->isVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductIsAlreadyVariantException($variant->getId());
		}

		if (!$this->variants->contains($variant)) {
			$this->variants->add($variant);
			$variant->setMainVariant($this);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 */
	private function addVariants(array $variants) {
		foreach ($variants as $variant) {
			$this->addVariant($variant);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function getVariants() {
		return $this->variants->toArray();
	}

	public function unsetMainVariant() {
		if (!$this->isVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductIsNotVariantException();
		}
		$this->variantType = self::VARIANT_TYPE_NONE;
		$this->mainVariant->variants->removeElement($this);
		$this->mainVariant = null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainVariant
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod) method is used but not through $this
	 */
	private function setMainVariant(Product $mainVariant) {
		$this->variantType = self::VARIANT_TYPE_VARIANT;
		$this->mainVariant = $mainVariant;
	}

	/**
	 * @param int $quantity
	 */
	public function addStockQuantity($quantity) {
		$this->stockQuantity += $quantity;
	}

	/**
	 * @param int $quantity
	 */
	public function subtractStockQuantity($quantity) {
		$this->stockQuantity -= $quantity;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductData $productData
	 */
	private function setTranslations(ProductData $productData) {
		foreach ($productData->name as $locale => $name) {
			$this->translation($locale)->setName($name);
		}
		foreach ($productData->variantAlias as $locale => $variantAlias) {
			$this->translation($locale)->setVariantAlias($variantAlias);
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\Product\ProductTranslation
	 */
	protected function createTranslation() {
		return new ProductTranslation();
	}

}
