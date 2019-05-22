<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Prezent\Doctrine\Translatable\Annotation as Prezent;
use Shopsys\FrameworkBundle\Model\Localization\AbstractTranslatableEntity;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Product\Availability\Availability;
use Shopsys\FrameworkBundle\Model\Product\Exception\ProductDomainNotFoundException;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;

/**
 * Product
 *
 * @ORM\Table(
 *     name="products",
 *     indexes={
 *         @ORM\Index(columns={"variant_type"})
 *     }
 * )
 * @ORM\Entity
 *
 * @method ProductTranslation translation(?string $locale = null)
 */
class Product extends AbstractTranslatableEntity
{
    public const OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY = 'setAlternateAvailability';
    public const OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE = 'excludeFromSale';
    public const OUT_OF_STOCK_ACTION_HIDE = 'hide';
    public const VARIANT_TYPE_NONE = 'none';
    public const VARIANT_TYPE_MAIN = 'main';
    public const VARIANT_TYPE_VARIANT = 'variant';

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Product\ProductTranslation[]
     *
     * @Prezent\Translations(targetEntity="Shopsys\FrameworkBundle\Model\Product\ProductTranslation")
     */
    protected $translations;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $catnum;

    /**
     * @var string
     *
     * @ORM\Column(type="tsvector", nullable=false)
     */
    protected $catnumTsvector;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $partno;

    /**
     * @var string
     *
     * @ORM\Column(type="tsvector", nullable=false)
     */
    protected $partnoTsvector;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $ean;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $vat;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $sellingFrom;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $sellingTo;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $sellingDenied;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $calculatedSellingDenied;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $hidden;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $calculatedHidden;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $usingStock;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $stockQuantity;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Unit\Unit")
     * @ORM\JoinColumn(name="unit_id", referencedColumnName="id", nullable=false)
     */
    protected $unit;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $outOfStockAction;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|null
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Availability\Availability")
     * @ORM\JoinColumn(name="availability_id", referencedColumnName="id", nullable=true)
     */
    protected $availability;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|null
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Availability\Availability")
     * @ORM\JoinColumn(name="out_of_stock_availability_id", referencedColumnName="id", nullable=true)
     */
    protected $outOfStockAvailability;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Availability\Availability")
     * @ORM\JoinColumn(name="calculated_availability_id", referencedColumnName="id", nullable=false)
     */
    protected $calculatedAvailability;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    protected $recalculateAvailability;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $calculatedVisibility;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain[]
     *
     * @ORM\OneToMany(
     *   targetEntity="Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain",
     *   mappedBy="product",
     *   orphanRemoval=true,
     *   cascade={"persist"}
     * )
     */
    protected $productCategoryDomains;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     *
     * @ORM\ManyToMany(targetEntity="Shopsys\FrameworkBundle\Model\Product\Flag\Flag")
     * @ORM\JoinTable(name="product_flags")
     */
    protected $flags;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    protected $recalculatePrice;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    protected $recalculateVisibility;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Brand\Brand")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $brand;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Product\Product[]
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product", mappedBy="mainVariant", cascade={"persist"})
     */
    protected $variants;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product", inversedBy="variants", cascade={"persist"})
     * @ORM\JoinColumn(name="main_variant_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $mainVariant;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $variantType;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $orderingPriority;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDomain[]|\Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Shopsys\FrameworkBundle\Model\Product\ProductDomain", mappedBy="product", cascade={"persist"}, fetch="EXTRA_LAZY")
     */
    protected $domains;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[]|null $variants
     */
    protected function __construct(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $variants = null)
    {
        $this->translations = new ArrayCollection();
        $this->domains = new ArrayCollection();
        $this->catnum = $productData->catnum;
        $this->partno = $productData->partno;
        $this->ean = $productData->ean;
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
        $this->createDomains($productData);
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
            $this->addVariants($variants, $productCategoryDomainFactory);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public static function create(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
    {
        return new static($productData, $productCategoryDomainFactory, null);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public static function createMainVariant(ProductData $productData, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory, array $variants)
    {
        return new static($productData, $productCategoryDomainFactory, $variants);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
     */
    public function edit(
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory,
        ProductData $productData,
        ProductPriceRecalculationScheduler $productPriceRecalculationScheduler
    ) {
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
        $this->setDomains($productData);

        if (!$this->isVariant()) {
            $this->setCategories($productCategoryDomainFactory, $productData->categoriesByDomainId);
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
        }

        $this->orderingPriority = $productData->orderingPriority;

        $productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($this);
        $this->markForVisibilityRecalculation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[] $flags
     */
    protected function editFlags(array $flags)
    {
        $this->flags->clear();
        foreach ($flags as $flag) {
            $this->flags->add($flag);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     */
    public function changeVat(Vat $vat)
    {
        $this->vat = $vat;
        $this->recalculatePrice = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $locale
     * @return string|null
     */
    public function getName($locale = null)
    {
        return $this->translation($locale)->getName();
    }

    /**
     * @param string|null $locale
     * @return string|null
     */
    public function getVariantAlias($locale = null)
    {
        return $this->translation($locale)->getVariantAlias();
    }

    /**
     * @return string[]
     */
    public function getNames()
    {
        $namesByLocale = [];

        foreach ($this->translations as $translation) {
            $namesByLocale[$translation->getLocale()] = $translation->getName();
        }

        return $namesByLocale;
    }

    /**
     * @return string|null
     */
    public function getCatnum()
    {
        return $this->catnum;
    }

    /**
     * @return string|null
     */
    public function getPartno()
    {
        return $this->partno;
    }

    /**
     * @return string|null
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @return \DateTime|null
     */
    public function getSellingFrom()
    {
        return $this->sellingFrom;
    }

    /**
     * @return \DateTime|null
     */
    public function getSellingTo()
    {
        return $this->sellingTo;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @return bool
     */
    public function getCalculatedHidden()
    {
        return $this->calculatedHidden;
    }

    /**
     * @return bool
     */
    public function isSellingDenied()
    {
        return $this->sellingDenied;
    }

    /**
     * @return bool
     */
    public function getCalculatedSellingDenied()
    {
        return $this->calculatedSellingDenied;
    }

    /**
     * @return bool
     */
    public function isUsingStock()
    {
        return $this->usingStock;
    }

    /**
     * @return int|null
     */
    public function getStockQuantity()
    {
        return $this->stockQuantity;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Unit\Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @return string
     */
    public function getOutOfStockAction()
    {
        return $this->outOfStockAction;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|null
     */
    public function getOutOfStockAvailability()
    {
        return $this->outOfStockAvailability;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Availability\Availability
     */
    public function getCalculatedAvailability()
    {
        return $this->calculatedAvailability;
    }

    /**
     * @return int
     */
    public function getOrderingPriority()
    {
        return $this->orderingPriority;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $availability
     */
    public function setAvailability(Availability $availability)
    {
        $this->availability = $availability;
        $this->recalculateAvailability = true;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability|null $outOfStockAvailability
     */
    public function setOutOfStockAvailability(Availability $outOfStockAvailability = null)
    {
        $this->outOfStockAvailability = $outOfStockAvailability;
        $this->recalculateAvailability = true;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Availability\Availability $calculatedAvailability
     */
    public function setCalculatedAvailability(Availability $calculatedAvailability)
    {
        $this->calculatedAvailability = $calculatedAvailability;
        $this->recalculateAvailability = false;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categoriesByDomainId
     */
    public function setCategories(
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory,
        array $categoriesByDomainId
    ) {
        foreach ($categoriesByDomainId as $domainId => $categories) {
            $this->removeOldProductCategoryDomains($categories, $domainId);
            $this->createNewProductCategoryDomains($productCategoryDomainFactory, $categories, $domainId);
        }

        if ($this->isMainVariant()) {
            foreach ($this->getVariants() as $variant) {
                $variant->setCategories($productCategoryDomainFactory, $categoriesByDomainId);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $newCategories
     * @param int $domainId
     */
    protected function createNewProductCategoryDomains(
        ProductCategoryDomainFactoryInterface $productCategoryDomainFactory,
        array $newCategories,
        $domainId
    ) {
        $currentProductCategoryDomainsOnDomainByCategoryId = $this->getProductCategoryDomainsByDomainIdIndexedByCategoryId($domainId);

        foreach ($newCategories as $newCategory) {
            if (!array_key_exists($newCategory->getId(), $currentProductCategoryDomainsOnDomainByCategoryId)) {
                $productCategoryDomain = $productCategoryDomainFactory->create($this, $newCategory, $domainId);
                $this->productCategoryDomains->add($productCategoryDomain);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $newCategories
     * @param int $domainId
     */
    protected function removeOldProductCategoryDomains(array $newCategories, $domainId)
    {
        $currentProductCategoryDomains = $this->getProductCategoryDomainsByDomainIdIndexedByCategoryId($domainId);

        foreach ($currentProductCategoryDomains as $currentProductCategoryDomain) {
            if (!in_array($currentProductCategoryDomain->getCategory(), $newCategories, true)) {
                $this->productCategoryDomains->removeElement($currentProductCategoryDomain);
            }
        }
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain[]
     */
    protected function getProductCategoryDomainsByDomainIdIndexedByCategoryId($domainId)
    {
        $productCategoryDomainsByCategoryId = [];

        foreach ($this->productCategoryDomains as $productCategoryDomain) {
            if ($productCategoryDomain->getDomainId() === $domainId) {
                $productCategoryDomainsByCategoryId[$productCategoryDomain->getCategory()->getId()] = $productCategoryDomain;
            }
        }

        return $productCategoryDomainsByCategoryId;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[][]
     */
    public function getCategoriesIndexedByDomainId()
    {
        $categoriesByDomainId = [];

        foreach ($this->productCategoryDomains as $productCategoryDomain) {
            $categoriesByDomainId[$productCategoryDomain->getDomainId()][] = $productCategoryDomain->getCategory();
        }

        return $categoriesByDomainId;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand|null
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return bool
     */
    protected function getCalculatedVisibility()
    {
        return $this->calculatedVisibility;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->getCalculatedVisibility();
    }

    public function markPriceAsRecalculated()
    {
        $this->recalculatePrice = false;
    }

    /**
     * @param bool $recalculateVisibility
     */
    protected function setRecalculateVisibility($recalculateVisibility)
    {
        $this->recalculateVisibility = $recalculateVisibility;
    }

    public function markForVisibilityRecalculation()
    {
        $this->setRecalculateVisibility(true);
        if ($this->isMainVariant()) {
            foreach ($this->getVariants() as $variant) {
                $variant->setRecalculateVisibility(true);
            }
        } elseif ($this->isVariant()) {
            $mainVariant = $this->getMainVariant();
            /**
             * When the product is fetched from persistence, the mainVariant is only a proxy object,
             * when we call something on this proxy object, Doctrine fetches it from persistence too.
             *
             * The problem is the Doctrine seems to not fetch the main variant when we only write something into it,
             * but when we read something first, Doctrine fetches the object, and the use-case works correctly.
             *
             * If you think this is strange and it shouldn't work even before the code was moved to Product, you are right, this is strange.
             * When the code is outside of Product, Doctrine does the job correctly, but once the code is inside of Product,
             * Doctrine seems to not fetching the main variant.
             */
            $mainVariant->isMarkedForVisibilityRecalculation();
            $mainVariant->setRecalculateVisibility(true);
        }
    }

    public function markForAvailabilityRecalculation()
    {
        $this->recalculateAvailability = true;
    }

    /**
     * @return bool
     */
    public function isMainVariant()
    {
        return $this->variantType === self::VARIANT_TYPE_MAIN;
    }

    /**
     * @return bool
     */
    public function isVariant()
    {
        return $this->variantType === self::VARIANT_TYPE_VARIANT;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getMainVariant()
    {
        if (!$this->isVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsNotVariantException();
        }

        return $this->mainVariant;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $variant
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    public function addVariant(self $variant, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
    {
        if (!$this->isMainVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\VariantCanBeAddedOnlyToMainVariantException(
                $this->getId(),
                $variant->getId()
            );
        }
        if ($variant->isMainVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\MainVariantCannotBeVariantException($variant->getId());
        }
        if ($variant->isVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyVariantException($variant->getId());
        }

        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setMainVariant($this);
            $variant->setCategories($productCategoryDomainFactory, $this->getCategoriesIndexedByDomainId());
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    protected function addVariants(array $variants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory)
    {
        foreach ($variants as $variant) {
            $this->addVariant($variant, $productCategoryDomainFactory);
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product[]
     */
    public function getVariants()
    {
        return $this->variants->toArray();
    }

    public function unsetMainVariant()
    {
        if (!$this->isVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsNotVariantException();
        }
        $this->variantType = self::VARIANT_TYPE_NONE;
        $this->mainVariant->variants->removeElement($this);
        $this->mainVariant = null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $mainVariant
     */
    protected function setMainVariant(self $mainVariant)
    {
        $this->variantType = self::VARIANT_TYPE_VARIANT;
        $this->mainVariant = $mainVariant;
    }

    /**
     * @param int $quantity
     */
    public function addStockQuantity($quantity)
    {
        $this->stockQuantity += $quantity;
    }

    /**
     * @param int $quantity
     */
    public function subtractStockQuantity($quantity)
    {
        $this->stockQuantity -= $quantity;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    protected function setTranslations(ProductData $productData)
    {
        foreach ($productData->name as $locale => $name) {
            $this->translation($locale)->setName($name);
        }
        foreach ($productData->variantAlias as $locale => $variantAlias) {
            $this->translation($locale)->setVariantAlias($variantAlias);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    protected function setDomains(ProductData $productData)
    {
        foreach ($this->domains as $productDomain) {
            $domainId = $productDomain->getDomainId();
            $productDomain->setSeoTitle($productData->seoTitles[$domainId]);
            $productDomain->setSeoH1($productData->seoH1s[$domainId]);
            $productDomain->setSeoMetaDescription($productData->seoMetaDescriptions[$domainId]);
            $productDomain->setDescription($productData->descriptions[$domainId]);
            $productDomain->setShortDescription($productData->shortDescriptions[$domainId]);
        }
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductDomain
     */
    protected function getProductDomain(int $domainId)
    {
        foreach ($this->domains as $domain) {
            if ($domain->getDomainId() === $domainId) {
                return $domain;
            }
        }

        throw new ProductDomainNotFoundException($this->id, $domainId);
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getShortDescription(int $domainId)
    {
        return $this->getProductDomain($domainId)->getShortDescription();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getDescription(int $domainId)
    {
        return $this->getProductDomain($domainId)->getDescription();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getSeoH1(int $domainId)
    {
        return $this->getProductDomain($domainId)->getSeoH1();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getSeoTitle(int $domainId)
    {
        return $this->getProductDomain($domainId)->getSeoTitle();
    }

    /**
     * @param int $domainId
     * @return string|null
     */
    public function getSeoMetaDescription(int $domainId)
    {
        return $this->getProductDomain($domainId)->getSeoMetaDescription();
    }

    /**
     * @return bool
     */
    public function isMarkedForVisibilityRecalculation()
    {
        return $this->recalculateVisibility;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductTranslation
     */
    protected function createTranslation()
    {
        return new ProductTranslation();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     */
    protected function createDomains(ProductData $productData)
    {
        $domainIds = array_keys($productData->seoTitles);

        foreach ($domainIds as $domainId) {
            $productDomain = new ProductDomain($this, $domainId);
            $this->domains[] = $productDomain;
        }

        $this->setDomains($productData);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductDeleteResult
     */
    public function getProductDeleteResult()
    {
        if ($this->isMainVariant()) {
            foreach ($this->getVariants() as $variantProduct) {
                $variantProduct->unsetMainVariant();
            }
        }
        if ($this->isVariant()) {
            return new ProductDeleteResult([$this->getMainVariant()]);
        }

        return new ProductDeleteResult();
    }

    public function checkIsNotMainVariant(): void
    {
        if ($this->isMainVariant()) {
            throw new \Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException($this->id);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $currentVariants
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    public function refreshVariants(array $currentVariants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory): void
    {
        $this->unsetRemovedVariants($currentVariants);
        $this->addNewVariants($currentVariants, $productCategoryDomainFactory);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $currentVariants
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactoryInterface $productCategoryDomainFactory
     */
    protected function addNewVariants(array $currentVariants, ProductCategoryDomainFactoryInterface $productCategoryDomainFactory): void
    {
        foreach ($currentVariants as $currentVariant) {
            if (!in_array($currentVariant, $this->getVariants(), true)) {
                $this->addVariant($currentVariant, $productCategoryDomainFactory);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $currentVariants
     */
    protected function unsetRemovedVariants(array $currentVariants)
    {
        foreach ($this->getVariants() as $originalVariant) {
            if (!in_array($originalVariant, $currentVariants, true)) {
                $originalVariant->unsetMainVariant();
            }
        }
    }
}
