<?php

namespace SS6\ShopBundle\Model\AdminMenu;

class MenuItem {

	const TYPE_REGULAR = 'regular';
	const TYPE_SETTINGS = 'settings';

	/**
	 * @var string
	 */
	private $label;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var \SS6\ShopBundle\Model\AdminMenu\MenuItem[]|null
	 */
	private $items;

	/**
	 * @var string|null
	 */
	private $route;

	/**
	 * @var array|null
	 */
	private $routeParameters;

	/**
	 * @param string $label
	 * @param string|null $type
	 * @param string $route
	 * @param array $routeParameters
	 * @param array $items
	 */
	public function __construct($label, $type = null, $route = null, array $routeParameters = null,
			array $items = null) {
		if (isset($type)) {
			$this->setType($type);
		} else {
			$this->setType(self::TYPE_REGULAR);
		}
		
		$this->label = $label;
		$this->route = $route;

		if (isset($routeParameters)) {
			$this->routeParameters = $routeParameters;
		} else {
			$this->routeParameters = array();
		}

		$this->items = $items;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminMenu\MenuItem[]|null
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @return type
	 */
	public function getRoute() {
		return $this->route;
	}

	/**
	 * @return array|null
	 */
	public function getRouteParameters() {
		return $this->routeParameters;
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminMenu\MenuItem $item
	 */
	public function addItem(MenuItem $item) {
		if (!isset($this->items)) {
			$this->items = array();
		}

		$this->items[] = $item;
	}

	/**
	 * @param string $route
	 */
	public function setRoute($route) {
		$this->route = $route;
	}

	/**
	 * @param string $route
	 */
	public function setRouteParameters(array $routeParameters) {
		$this->routeParameters = $routeParameters;
	}

	/**
	 * @param array $items
	 */
	public function setItems(array $items) {
		$this->items = $items;
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		if (!in_array($type, $this->getTypes())) {
			throw new \SS6\ShopBundle\Model\AdminMenu\Exception\InvalidItemTypeException(
				$type . ' is not a valid item type. Supported types are: ' . implode(', ', $this->getTypes()) . '.'
			);
		}
		$this->type = $type;
	}

	/**
	 * @return array
	 */
	private function getTypes() {
		return array(
			self::TYPE_REGULAR,
			self::TYPE_SETTINGS,
		);
	}

}
