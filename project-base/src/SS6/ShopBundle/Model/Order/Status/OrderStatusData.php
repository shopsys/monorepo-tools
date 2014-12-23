<?php

namespace SS6\ShopBundle\Model\Order\Status;

use SS6\ShopBundle\Model\Order\Status\OrderStatus;

class OrderStatusData {

	/**
	 * @var string[]
	 */
	private $name;

	/**
	 * @param string[] $name
	 */
	public function __construct(array $name = []) {
		$this->name = $name;
	}

	/**
	 * @return string[]
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string[] $name
	 */
	public function setName(array $name) {
		$this->name = $name;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatus $orderStatus
	 */
	public function setFromEntity(OrderStatus $orderStatus) {
		$translations = $orderStatus->getTranslations();
		$names = [];
		foreach ($translations as $translate) {
			$names[$translate->getLocale()] = $translate->getName();
		}
		$this->setName($names);
	}

}
