<?php

namespace SS6\ShopBundle\Model\Module;

use SS6\ShopBundle\Component\ConstantList\AbstractTranslatedConstantList;

class ModuleList extends AbstractTranslatedConstantList {

	const ACCESSORIES_ON_BUY = 'accessoriesOnBuy';

	/**
	 * {@inheritDoc}
	 */
	public function getTranslationsIndexedByValue() {
		return [
			self::ACCESSORIES_ON_BUY => $this->translator->trans('Příslušenství v mezikošíku'),
		];
	}

}
