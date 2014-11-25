<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusData {

	/**
	 * @var array
	 */
	private $names;

	/**
	 * @param array $names
	 */
	public function __construct($names = array()) {
		$this->names = $names;
	}

	/**
	 * @return array
	 */
	public function getNames() {
		return $this->names;
	}

	/**
	 * @param array $names
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
