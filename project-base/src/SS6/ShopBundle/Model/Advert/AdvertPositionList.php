<?php

namespace SS6\ShopBundle\Model\Advert;

use SS6\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class AdvertPositionList extends AbstractTranslatedConstantList {

	const POSITION_HEADER = 'header';
	const POSITION_FOOTER = 'footer';
	const POSITION_PRODUCT_LIST = 'productList';
	const POSITION_LEFT_SIDEBAR = 'leftSidebar';

	/**
	 * @inheritdoc
	 */
	public function getTranslationsIndexedByValue() {
		return [
			self::POSITION_HEADER => t('pod hlavičkou'),
			self::POSITION_FOOTER => t('nad patičkou'),
			self::POSITION_PRODUCT_LIST => t('v kategorii (nad názvem kategorie)'),
			self::POSITION_LEFT_SIDEBAR => t('v levém panelu (pod stromem kategorií)'),
		];
	}

}
