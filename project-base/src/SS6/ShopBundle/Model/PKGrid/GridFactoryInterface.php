<?php

namespace SS6\ShopBundle\Model\PKGrid;

interface GridFactoryInterface {

	/**
	 * @return SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function create();
}
