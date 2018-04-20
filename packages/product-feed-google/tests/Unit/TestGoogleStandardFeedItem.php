<?php

namespace Tests\ProductFeed\GoogleBundle\Unit;

use Shopsys\ProductFeed\StandardFeedItemInterface;

class TestGoogleStandardFeedItem implements StandardFeedItemInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $productName;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string|null
     */
    private $imgUrl;

    /**
     * @var string
     */
    private $priceVat;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string|null
     */
    private $ean;

    /**
     * @var int|null
     */
    private $deliveryDate;

    /**
     * @var string|null
     */
    private $manufacturer;

    /**
     * @var string|null
     */
    private $categoryText;

    /**
     * @var string[]
     */
    private $parametersByName;

    /**
     * @var string|null
     */
    private $partno;

    /**
     * @var int|null
     */
    private $mainVariantId;

    /**
     * @var array
     */
    private $customValues;

    /**
     * @var bool
     */
    private $sellingDenied;

    /**
     * @param int $id
     * @param string $productName
     * @param string $description
     * @param string $url
     * @param string|null $imgUrl
     * @param string $priceVat
     * @param string $currencyCode
     * @param string|null $ean
     * @param int|null $deliveryDate
     * @param string|null $manufacturer
     * @param string|null $categoryText
     * @param string[] $parametersByName
     * @param string|null $partno
     * @param int|null $mainVariantId
     * @param bool $sellingDenied
     */
    public function __construct(
        $id,
        $productName,
        $description,
        $url,
        $imgUrl,
        $priceVat,
        $currencyCode,
        $ean,
        $deliveryDate,
        $manufacturer,
        $categoryText,
        $parametersByName,
        $partno,
        $mainVariantId,
        $sellingDenied
    ) {
        $this->id = $id;
        $this->productName = $productName;
        $this->description = $description;
        $this->url = $url;
        $this->imgUrl = $imgUrl;
        $this->priceVat = $priceVat;
        $this->ean = $ean;
        $this->deliveryDate = $deliveryDate;
        $this->manufacturer = $manufacturer;
        $this->categoryText = $categoryText;
        $this->parametersByName = $parametersByName;
        $this->partno = $partno;
        $this->mainVariantId = $mainVariantId;
        $this->customValues = [];
        $this->sellingDenied = $sellingDenied;
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }

    /**
     * @return string
     */
    public function getPriceVat()
    {
        return $this->priceVat;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @return string|null
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @return int|null
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @return string|null
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @return string|null
     */
    public function getCategoryText()
    {
        return $this->categoryText;
    }

    /**
     * @return string[]
     */
    public function getParametersByName()
    {
        return $this->parametersByName;
    }

    /**
     * @return string|null
     */
    public function getPartno()
    {
        return $this->partno;
    }

    /**
     * @return int|null
     */
    public function getMainVariantId()
    {
        return $this->mainVariantId;
    }

    /**
     * @return int
     */
    public function getMainCategoryId()
    {
        return 0;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getCustomValue($name)
    {
        return $this->customValues[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setCustomValue($name, $value)
    {
        $this->customValues[$name] = $value;
    }

    /**
     * @return bool
     */
    public function isSellingDenied()
    {
        return $this->sellingDenied;
    }
}
