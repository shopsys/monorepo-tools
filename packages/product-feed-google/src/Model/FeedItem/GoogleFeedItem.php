<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Model\Feed\FeedItemInterface;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Price;

class GoogleFeedItem implements FeedItemInterface
{
    const IDENTIFIER_TYPE_EAN = 'gtin';
    const IDENTIFIER_TYPE_PARTNO = 'mpn';

    const AVAILABILITY_OUT_OF_STOCK = 'out of stock';
    const AVAILABILITY_IN_STOCK = 'in stock';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $brandName;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string|null
     */
    protected $ean;

    /**
     * @var string|null
     */
    protected $partno;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string|null
     */
    protected $imgUrl;

    /**
     * @var bool
     */
    protected $sellingDenied;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected $price;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    protected $currency;

    public function __construct(
        int $id,
        string $name,
        ?string $brandName,
        ?string $description,
        ?string $ean,
        ?string $partno,
        string $url,
        ?string $imgUrl,
        bool $sellingDenied,
        Price $price,
        Currency $currency
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->brandName = $brandName;
        $this->description = $description;
        $this->ean = $ean;
        $this->partno = $partno;
        $this->url = $url;
        $this->imgUrl = $imgUrl;
        $this->sellingDenied = $sellingDenied;
        $this->price = $price;
        $this->currency = $currency;
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getBrand(): ?string
    {
        return $this->brandName;
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
    public function getLink(): string
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getImageLink(): ?string
    {
        return $this->imgUrl;
    }

    /**
     * @return string
     */
    public function getAvailability(): string
    {
        return $this->sellingDenied ? self::AVAILABILITY_OUT_OF_STOCK : self::AVAILABILITY_IN_STOCK;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        return array_filter([
            self::IDENTIFIER_TYPE_EAN => $this->ean,
            self::IDENTIFIER_TYPE_PARTNO => $this->partno,
        ]);
    }
}
