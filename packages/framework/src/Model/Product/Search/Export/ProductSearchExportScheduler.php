<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

class ProductSearchExportScheduler
{
    protected $productIds = [];

    /**
     * @param int $productId
     */
    public function scheduleProductIdForImmediateExport(int $productId): void
    {
        $this->productIds[] = $productId;
    }

    /**
     * @return bool
     */
    public function hasAnyProductIdsForImmediateExport(): bool
    {
        return $this->productIds !== [];
    }

    /**
     * @return int[]
     */
    public function getProductIdsForImmediateExport(): array
    {
        return array_unique($this->productIds);
    }

    public function cleanScheduleForImmediateExport(): void
    {
        $this->productIds = [];
    }
}
