<?php

namespace Shopsys\ShopBundle\Component\Grid;

use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use Twig_Environment;

class GridFactory
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector
     */
    private $routeCsrfProtector;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Shopsys\ShopBundle\Component\Grid\Ordering\GridOrderingService
     */
    private $gridOrderingService;

    public function __construct(
        RequestStack $requestStack,
        Router $router,
        RouteCsrfProtector $routeCsrfProtector,
        Twig_Environment $twig
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->routeCsrfProtector = $routeCsrfProtector;
        $this->twig = $twig;
    }

    /**
     * @param string $gridId
     * @param \Shopsys\ShopBundle\Component\Grid\DataSourceInterface $dataSource
     * @return \Shopsys\ShopBundle\Component\Grid\Grid
     */
    public function create($gridId, DataSourceInterface $dataSource)
    {
        return new Grid(
            $gridId,
            $dataSource,
            $this->requestStack,
            $this->router,
            $this->routeCsrfProtector,
            $this->twig
        );
    }
}
