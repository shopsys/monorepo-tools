<?php

namespace SS6\ShopBundle\Component\Grid;

interface GridFactoryInterface {

	/**
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create();
}
