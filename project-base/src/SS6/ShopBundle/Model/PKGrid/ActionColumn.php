<?php

namespace SS6\ShopBundle\Model\PKGrid;

use SS6\ShopBundle\Model\PKGrid\PKGrid;
use Symfony\Component\Routing\Router;

class ActionColumn {

	const TYPE_DELETE = 'delete';
	const TYPE_EDIT = 'edit';
	
	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $route;

	/**
	 * @var array
	 */
	private $bindingRouteParams;

	/**
	 * @var arraty
	 */
	private $additionalRouteParams;

	/**
	 * @var string|null
	 */
	private $class;

	/**
	 * @var string|null
	 */
	private $confirmMessage;

	/**
	 *
	 * @param \Symfony\Component\Routing\Router $router
	 * @param string $type
	 * @param string $name
	 * @param \Symfony\Component\Routing\Router $route
	 * @param array $bindingRouteParams
	 * @param array $additionalRouteParams
	 */
	public function __construct(Router $router, $type, $name, $route, array $bindingRouteParams, array $additionalRouteParams) {
		$this->router = $router;
		$this->type = $type;
		$this->name = $name;
		$this->route = $route;
		$this->bindingRouteParams = $bindingRouteParams;
		$this->additionalRouteParams = $additionalRouteParams;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string|null
	 */
	public function getClass() {
		return $this->class;
	}

	/**
	 * @return string|null
	 */
	public function getConfirmMessage() {
		return $this->confirmMessage;
	}

	/**
	 * @param string $class
	 * @return \SS6\ShopBundle\Model\PKGrid\ActionColumn
	 */
	public function setClass($class) {
		$this->class = $class;

		return $this;
	}

	/**
	 * @param string $confirmMessage
	 * @return \SS6\ShopBundle\Model\PKGrid\ActionColumn
	 */
	public function setConfirmMessage($confirmMessage) {
		$this->confirmMessage = $confirmMessage;

		return $this;
	}

	/**
	 * @param array $row
	 * @param string|null $value
	 * @return string
	 */
	public function getTargetUrl(array $row) {
		$parameters = $this->additionalRouteParams;

		foreach ($this->bindingRouteParams as $key => $queryId) {
			$parameters[$key] = PKGrid::getValueFromRowByQueryId($row, $queryId);
		}
		
		return $this->router->generate($this->route, $parameters, true);
	}

}
