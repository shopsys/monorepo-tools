<?php

namespace SS6\ShopBundle\Model\Module;

class ModuleList {

	const ACCESSORIES_ON_BUY = 'accessoriesOnBuy';

	/**
	 * @return string
	 */
	public function getAll() {
		return [
			self::ACCESSORIES_ON_BUY,
		];
	}

}
