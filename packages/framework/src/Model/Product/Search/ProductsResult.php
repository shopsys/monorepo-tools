<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search;

class ProductsResult
{
    /**
     * @var int
     */
    protected $total;

    /**
     * @var array
     */
    protected $hits;

    /**
     * @param int $total
     * @param array $hits
     */
    public function __construct(int $total, array $hits)
    {
        $this->total = $total;
        $this->hits = $hits;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return array
     */
    public function getHits(): array
    {
        return $this->hits;
    }
}
