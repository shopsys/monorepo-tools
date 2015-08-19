<?php

namespace SS6\ShopBundle\Model\Article;

use SS6\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class ArticlePlacementList extends AbstractTranslatedConstantList {

	const PLACEMENT_TOP_MENU = 'topMenu';
	const PLACEMENT_FOOTER = 'footer';

	/**
	 * @inheritdoc
	 */
	public function getTranslationsIndexedByValue() {
		return [
			self::PLACEMENT_TOP_MENU => $this->translator->trans('v horním menu'),
			self::PLACEMENT_FOOTER => $this->translator->trans('v patičce'),
		];
	}

}
