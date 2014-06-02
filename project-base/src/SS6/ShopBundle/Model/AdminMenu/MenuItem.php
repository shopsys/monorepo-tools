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
	 * @var boolean
	 */
	private $visible;

	/**
	 * @param string $label
	 * @param string|null $type
	 * @param string $route
	 * @param array $routeParameters
	 * @param array $items
	 */
	public function __construct($label, $type = null, $route = null, array $routeParameters = null,
			$visible = true, array $items = null) {
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

		if (isset($visible)) {
			$this->visible = $visible;
		} else {
			$this->visible = true;
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
	 * @return string|null
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
	 * @return boolean
	 */
	public function isVisible() {
		return $this->visible === true;
	}

	/**
	 * @param string $type
	 */
	private function setType($type) {
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
