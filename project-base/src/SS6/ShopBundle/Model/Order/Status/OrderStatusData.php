<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;

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

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 */
	public function setFromEntity(OrderStatus $orderStatus) {
		$translations = $orderStatus->getTranslations();
		$names = array();
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setNames($names);
	}

}
