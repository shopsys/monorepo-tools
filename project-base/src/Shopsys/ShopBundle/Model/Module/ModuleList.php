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
    public function getNames()
    {
        return array_keys($this->getLabelsIndexedByName());
    }

    /**
     * @return string[]
     */
    public function getNamesIndexedByLabel()
    {
        $labelsIndexedByNames = $this->getLabelsIndexedByName();
        $namesIndexedByLabel = array_flip($labelsIndexedByNames);
        if (count($labelsIndexedByNames) !== count($namesIndexedByLabel)) {
            throw new \Shopsys\ShopBundle\Model\Module\Exception\NotUniqueModuleLabelException($labelsIndexedByNames);
        }

        return $namesIndexedByLabel;
    }

    /**
     * @return string[]
     */
    private function getLabelsIndexedByName()
    {
        return [
            self::ACCESSORIES_ON_BUY => t('Accessories in purchase confirmation box'),
            self::PRODUCT_FILTER_COUNTS => t('Number of products in filter'),
            self::PRODUCT_STOCK_CALCULATIONS => t('Automatic stock calculation'),
        ];
    }
}
