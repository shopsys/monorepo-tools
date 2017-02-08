<?php

namespace SS6\ShopBundle\Model\Module;

use SS6\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class ModuleList extends AbstractTranslatedConstantList {

	const ACCESSORIES_ON_BUY = 'accessoriesOnBuy';
	const PRODUCT_FILTER_COUNTS = 'productFilterCounts';
	const PRODUCT_STOCK_CALCULATIONS = 'productStockCalculations';

	/**
	 * {@inheritDoc}
	 */
	public function getTranslationsIndexedByValue() {
		return [
			self::ACCESSORIES_ON_BUY => t('Accessories in purchase confirmation box'),
			self::PRODUCT_FILTER_COUNTS => t('Number of products in filter'),
			self::PRODUCT_STOCK_CALCULATIONS => t('Automatic stock calculation'),
		];
	}

}
