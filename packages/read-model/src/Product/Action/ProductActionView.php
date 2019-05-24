<?php

declare(strict_types=1);

namespace Shopsys\ReadModelBundle\Product\Action;

/**
 * @experimental
 *
 * Class representing products actions in lists in frontend
 *
 * @see \Shopsys\ReadModelBundle\Product\Listed\ListedProductView
 * @see https://github.com/shopsys/shopsys/blob/master/docs/model/introduction-to-read-model.md
 */
class ProductActionView
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var bool
     */
    protected $sellingDenied;

    /**
     * @var bool
     */
    protected $mainVariant;

    /**
     * @var string
     */
    protected $detailUrl;

    /**
     * @param int $id
     * @param bool $sellingDenied
     * @param bool $mainVariant
     * @param string $detailUrl
     */
    public function __construct(int $id, bool $sellingDenied, bool $mainVariant, string $detailUrl)
    {
        $this->id = $id;
        $this->sellingDenied = $sellingDenied;
        $this->mainVariant = $mainVariant;
        $this->detailUrl = $detailUrl;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isSellingDenied(): bool
    {
        return $this->sellingDenied;
    }

    /**
     * @return bool
     */
    public function isMainVariant(): bool
    {
        return $this->mainVariant;
    }

    /**
     * @return string
     */
    public function getDetailUrl(): string
    {
        return $this->detailUrl;
    }
}
