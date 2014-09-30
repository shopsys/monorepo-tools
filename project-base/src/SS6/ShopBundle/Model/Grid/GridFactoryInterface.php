<?php

namespace SS6\ShopBundle\Model\Grid;

interface GridFactoryInterface {

	/**
	 * @return SS6\ShopBundle\Model\Grid\Grid
	 */
	public function create();
}
