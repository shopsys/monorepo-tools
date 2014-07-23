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

		foreach ($this->bindingRouteParams as $key => $queryId) {
			$queryIdParts = explode('.', $queryId);

			if (count($queryIdParts) === 1) {
				$value = $row[$queryIdParts[0]];
			} elseif (count($queryIdParts) === 2) {
				if (array_key_exists($queryIdParts[0], $row) && array_key_exists($queryIdParts[1], $row[$queryIdParts[0]])) {
					$value = $row[$queryIdParts[0]][$queryIdParts[1]];
				} elseif (array_key_exists($queryIdParts[1], $row)) {
					$value = $row[$queryIdParts[1]];
				} else {
					$value = $row[$column->getQueryId()];
				}
			}

			$parameters[$key] = $value;
		}
		
		return $this->router->generate($this->route, $parameters, true);
	}

}
