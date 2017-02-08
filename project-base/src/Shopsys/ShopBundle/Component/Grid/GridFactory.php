<?php

namespace SS6\ShopBundle\Component\Grid;

use SS6\ShopBundle\Component\Grid\DataSourceInterface;
use SS6\ShopBundle\Component\Grid\Grid;
use SS6\ShopBundle\Component\Grid\Ordering\GridOrderingService;
use SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

class GridFactory {

	/**
	 * @var \Symfony\Component\HttpFoundation\RequestStack
	 */
	private $requestStack;

	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @var \SS6\ShopBundle\Component\Router\Security\RouteCsrfProtector
	 */
	private $routeCsrfProtector;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var \SS6\ShopBundle\Component\Grid\Ordering\GridOrderingService
	 */
	private $gridOrderingService;

	public function __construct(
		RequestStack $requestStack,
		Router $router,
		RouteCsrfProtector $routeCsrfProtector,
		Twig_Environment $twig,
		GridOrderingService $gridOrderingService
	) {
		$this->requestStack = $requestStack;
		$this->router = $router;
		$this->routeCsrfProtector = $routeCsrfProtector;
		$this->twig = $twig;
		$this->gridOrderingService = $gridOrderingService;
	}

	/**
	 * @param string $gridId
	 * @param \SS6\ShopBundle\Component\Grid\DataSourceInterface $dataSource
	 * @return \SS6\ShopBundle\Component\Grid\Grid
	 */
	public function create($gridId, DataSourceInterface $dataSource) {
		return new Grid(
			$gridId,
			$dataSource,
			$this->requestStack,
			$this->router,
			$this->routeCsrfProtector,
			$this->twig,
			$this->gridOrderingService
		);
	}
}
