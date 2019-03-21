<?php

namespace Shopsys\FrameworkBundle\Model\Module;

class ModuleList
{
    public const ACCESSORIES_ON_BUY = 'accessoriesOnBuy';
    public const PRODUCT_FILTER_COUNTS = 'productFilterCounts';
    public const PRODUCT_STOCK_CALCULATIONS = 'productStockCalculations';

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
            throw new \Shopsys\FrameworkBundle\Model\Module\Exception\NotUniqueModuleLabelException($labelsIndexedByNames);
        }

        return $namesIndexedByLabel;
    }

    /**
     * @return string[]
     */
    protected function getLabelsIndexedByName()
    {
        return [
            self::ACCESSORIES_ON_BUY => t('Accessories in purchase confirmation box'),
            self::PRODUCT_FILTER_COUNTS => t('Number of products in filter'),
            self::PRODUCT_STOCK_CALCULATIONS => t('Automatic stock calculation'),
        ];
    }
}
