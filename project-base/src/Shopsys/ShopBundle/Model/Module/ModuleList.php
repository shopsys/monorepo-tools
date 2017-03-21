<?php

namespace Shopsys\ShopBundle\Model\Module;

class ModuleList
{
    const ACCESSORIES_ON_BUY = 'accessoriesOnBuy';
    const PRODUCT_FILTER_COUNTS = 'productFilterCounts';
    const PRODUCT_STOCK_CALCULATIONS = 'productStockCalculations';

    /**
     * @return string[]
     */
    public function getModuleNamesIndexedByLabel()
    {
        return [
            t('Accessories in purchase confirmation box') => self::ACCESSORIES_ON_BUY,
            t('Number of products in filter') => self::PRODUCT_FILTER_COUNTS,
            t('Automatic stock calculation') => self::PRODUCT_STOCK_CALCULATIONS,
        ];
    }
}
