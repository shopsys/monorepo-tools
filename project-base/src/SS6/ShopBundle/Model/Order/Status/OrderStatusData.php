<?php

namespace SS6\ShopBundle\Model\Order\Status;

class OrderStatusData {

	/**
	 * @var array|null
	 */
	private $names;

	/**
	 * @return array|null
	 */
	public function getNames() {
		return $this->names;
	}

	/**
	 * @param array|null $names
	 */
	public function setNames($names) {
		$this->names = $names;
	}

}
