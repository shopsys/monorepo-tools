<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Model\Feed\FeedItemInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Price;

class HeurekaFeedItem implements FeedItemInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int|null
     */
    protected $mainVariantId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string|null
     */
    protected $imgUrl;

    /**
     * @var string|null
     */
    protected $brandName;

    /**
     * @var string|null
     */
    protected $ean;

    /**
     * @var int|null
     */
    protected $availabilityDispatchTime;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected $price;

    /**
     * @var string|null
     */
    protected $heurekaCategoryFullName;

    /**
     * @var string[]
     */
    protected $parametersByName;

    /**
     * @var float|null
     */
    protected $cpc;

    public function __construct(
        int $id,
        ?int $mainVariantId,
        string $name,
        ?string $description,
        string $url,
        ?string $imgUrl,
        ?string $brandName,
        ?string $ean,
        ?int $availabilityDispatchTime,
        Price $price,
        ?string $heurekaCategoryFullName,
        array $parametersByName,
        ?float $cpc
    ) {
        $this->id = $id;
        $this->mainVariantId = $mainVariantId;
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->imgUrl = $imgUrl;
        $this->brandName = $brandName;
        $this->ean = $ean;
        $this->availabilityDispatchTime = $availabilityDispatchTime;
        $this->price = $price;
        $this->heurekaCategoryFullName = $heurekaCategoryFullName;
        $this->parametersByName = $parametersByName;
        $this->cpc = $cpc;
    }

    /**
     * @return int
     */
    public function getSeekId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getGroupId(): ?int
    {
        return $this->mainVariantId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return string|null
     */
    public function getEan(): ?string
    {
        return $this->ean;
    }

    /**
     * @return int|null
     */
    public function getDeliveryDate(): ?int
    {
        return $this->availabilityDispatchTime;
    }

    /**
     * @return string|null
     */
    public function getManufacturer(): ?string
    {
        return $this->brandName;
    }

    /**
     * @return string|null
     */
    public function getCategoryText(): ?string
    {
        return $this->heurekaCategoryFullName;
    }

    /**
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parametersByName;
    }

    /**
     * @return float|null
     */
    public function getCpc(): ?float
    {
        return $this->cpc;
    }
}
