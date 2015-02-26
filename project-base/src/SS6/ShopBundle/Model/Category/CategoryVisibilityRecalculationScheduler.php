<?php

namespace SS6\ShopBundle\Model\Category;

class CategoryVisibilityRecalculationScheduler {

	/**
	 * @var boolean
	 */
	private $recaluculate = false;

	public function scheduleRecalculation() {
		$this->recaluculate = true;
	}

	/**
	 * @return boolean
	 */
	public function isRecalculationScheduled() {
		return $this->recaluculate;
	}

}
