<?php

namespace SS6\ShopBundle\Model\PKGrid;

use Symfony\Component\Routing\Router;

class ActionColumn {
	
	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;
	
	private $type;
	private $name;
	private $route;
	private $bindingRouteParams;
	private $additionalRouteParams;
	private $class;
	private $confirmMessage;

	public function __construct(Router $router, $type, $name, $route, array $bindingRouteParams, array $additionalRouteParams) {
		$this->router = $router;
		$this->type = $type;
		$this->name = $name;
		$this->route = $route;
		$this->bindingRouteParams = $bindingRouteParams;
		$this->additionalRouteParams = $additionalRouteParams;
	}

	public function getType() {
		return $this->type;
	}

	public function getName() {
		return $this->name;
	}

	public function getClass() {
		return $this->class;
	}

	public function getConfirmMessage() {
		return $this->confirmMessage;
	}

	public function setClass($class) {
		$this->class = $class;

		return $this;
	}

	public function setConfirmMessage($confirmMessage) {
		$this->confirmMessage = $confirmMessage;

		return $this;
	}

	public function getTargetUrl($row) {
		$parameters = $this->additionalRouteParams;
		
		foreach ($this->bindingRouteParams as $key => $value) {
			$queryIdParts = explode('.', $value);
			$columnIndex = array_pop($queryIdParts);
			if (array_key_exists($columnIndex, $row)) {
				$parameters[$key] = $row[$columnIndex];
			}
		}
		
		return $this->router->generate($this->route, $parameters, true);
	}

}
