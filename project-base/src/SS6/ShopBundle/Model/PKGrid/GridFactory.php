<?php

namespace SS6\ShopBundle\Model\PKGrid;

use SS6\ShopBundle\Model\PKGrid\PKGrid;
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
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 * @param \Symfony\Component\Routing\Router $router
	 * @param \Twig_Environment $twig
	 */
	public function __construct(RequestStack $requestStack, Router $router, Twig_Environment $twig) {
		$this->requestStack = $requestStack;
		$this->router = $router;
		$this->twig = $twig;
	}

	/**
	 * @param string $gridId
	 * @return \SS6\ShopBundle\Model\PKGrid\PKGrid
	 */
	public function get($gridId) {
		return new PKGrid($gridId, $this->requestStack, $this->router, $this->twig);
	}
}
