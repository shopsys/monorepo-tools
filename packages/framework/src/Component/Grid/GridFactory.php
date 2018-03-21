<?php

namespace Shopsys\FrameworkBundle\Component\Grid;

use Shopsys\FrameworkBundle\Component\Router\Security\RouteCsrfProtector;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig_Environment;

class GridFactory
{
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\Security\RouteCsrfProtector
     */
    private $routeCsrfProtector;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
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
     * @param \Shopsys\FrameworkBundle\Component\Grid\DataSourceInterface $dataSource
     * @return \Shopsys\FrameworkBundle\Component\Grid\Grid
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
