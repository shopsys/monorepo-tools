<?php

namespace Shopsys\ShopBundle\Model\Feed\Zbozi;

use Shopsys\ProductFeed\FeedItemInterface;

class ZboziItem implements FeedItemInterface
{
    /**
     * @var int
     */
    private $itemId;

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
     * @var string|null
     */
    private $cpc;

    /**
     * @var string|null
     */
    private $cpcSearch;

    /**
     * @var int|null
     */
    private $mainVariantId;

    /**
     * @param int $itemId
     * @param string $productName
     * @param string $description
     * @param string $url
     * @param string|null $imgUrl
     * @param string $priceVat
     * @param string|null $ean
     * @param int $deliveryDate
     * @param string|null $manufacturer
     * @param string|null $categoryText
     * @param string[] $parametersByName
     * @param string|null $partno
     * @param string|null $cpc
     * @param string|null $cpcSearch
     * @param int|null $mainVariantId
     */
    public function __construct(
        $itemId,
        $productName,
        $description,
        $url,
        $imgUrl,
        $priceVat,
        $ean,
        $deliveryDate,
        $manufacturer,
        $categoryText,
        $parametersByName,
        $partno,
        $cpc,
        $cpcSearch,
        $mainVariantId
    ) {
        $this->itemId = $itemId;
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
        $this->cpc = $cpc;
        $this->cpcSearch = $cpcSearch;
        $this->mainVariantId = $mainVariantId;
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
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
     * @return string|null
     */
    public function getCpc()
    {
        return $this->cpc;
    }

    /**
     * @return string|null
     */
    public function getCpcSearch()
    {
        return $this->cpcSearch;
    }

    /**
     * @return int|null
     */
    public function getMainVariantId()
    {
        return $this->mainVariantId;
    }
}
