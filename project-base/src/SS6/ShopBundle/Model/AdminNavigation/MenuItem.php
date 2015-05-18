<?php

namespace SS6\ShopBundle\Model\AdminNavigation;

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
	 * @var \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]|null
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
	 * @var boolean
	 */
	private $superadmin;

	/**
	 * @param string $label
	 * @param string|null $type
	 * @param string|null $route
	 * @param array|null $routeParameters
	 * @param boolean $visible
	 * @param boolean $superadmin
	 * @param array|null $items
	 */
	public function __construct(
		$label,
		$type = null,
		$route = null,
		array $routeParameters = null,
		$visible = true,
		$superadmin = false,
		array $items = null
	) {
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
			$this->routeParameters = [];
		}

		if (isset($visible)) {
			$this->visible = $visible;
		} else {
			$this->visible = true;
		}

		if (isset($superadmin)) {
			$this->superadmin = $superadmin;
		} else {
			$this->superadmin = false;
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
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]|null
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
	 * @return boolean
	 */
	public function isSuperadmin() {
		return $this->superadmin === true;
	}

	/**
	 * @param string $type
	 */
	private function setType($type) {
		if (!in_array($type, $this->getTypes())) {
			throw new \SS6\ShopBundle\Model\AdminNavigation\Exception\InvalidItemTypeException(
				$type . ' is not a valid item type. Supported types are: ' . implode(', ', $this->getTypes()) . '.'
			);
		}
		$this->type = $type;
	}

	/**
	 * @return array
	 */
	private function getTypes() {
		return [
			self::TYPE_REGULAR,
			self::TYPE_SETTINGS,
		];
	}

}
