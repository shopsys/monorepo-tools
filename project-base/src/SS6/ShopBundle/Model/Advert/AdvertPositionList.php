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
			self::POSITION_HEADER => $this->translator->trans('pod hlavičkou'),
			self::POSITION_FOOTER => $this->translator->trans('nad patičkou'),
			self::POSITION_PRODUCT_LIST => $this->translator->trans('v kategorii (nad názvem kategorie)'),
			self::POSITION_LEFT_SIDEBAR => $this->translator->trans('v levém panelu (pod stromem kategorií)'),
		];
	}

}
