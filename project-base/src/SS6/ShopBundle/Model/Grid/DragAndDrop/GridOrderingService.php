<?php

namespace SS6\ShopBundle\Model\Grid\DragAndDrop;

use SS6\ShopBundle\Model\Grid\DragAndDrop\GridOrderingInterface;
use Symfony\Component\DependencyInjection\Container;

class GridOrderingService {

	/**
	 * @var \Symfony\Component\DependencyInjection\Container
	 */
	private $container;

	/**
	 * @param \Symfony\Component\DependencyInjection\Container $container
	 */
	public function __construct(Container $container) {
		$this->container = $container;
	}

	/**
	 * @param string $serviceName
	 * @param array $rowIds
	 */
	public function saveOrder($serviceName, array $rowIds) {
		$orderingService = $this->getOrderingService($serviceName);
		$orderingService->saveOrder($rowIds);
	}

	/**
	 * @param string $serviceName
	 * @return \SS6\ShopBundle\Model\Grid\DragAndDrop\GridOrderingInterface
	 */
	private function getOrderingService($serviceName) {
		$orderingService = $this->container->get($serviceName, Container::NULL_ON_INVALID_REFERENCE);

		if ($orderingService instanceof GridOrderingInterface) {
			return $orderingService;
		} else {
			throw new \SS6\ShopBundle\Model\Grid\DragAndDrop\Exception\InvalidServiceException($serviceName);
		}
	}

}
