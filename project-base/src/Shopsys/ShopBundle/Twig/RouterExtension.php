<?php

namespace Shopsys\ShopBundle\Twig;

use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Twig_Extension;
use Twig_SimpleFunction;

class RouterExtension extends Twig_Extension
{
    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    /**
     * @param \Shopsys\ShopBundle\Component\Router\DomainRouterFactory $domainRouterFactory
     */
    public function __construct(DomainRouterFactory $domainRouterFactory)
    {
        $this->domainRouterFactory = $domainRouterFactory;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'findUrlByDomainId',
                [$this, 'findUrlByDomainId']
            ),
        ];
    }

    /**
     * @param string $route
     * @param array $routeParams
     * @param int $domainId
     * @param bool $absolute
     * @return string|null
     */
    public function findUrlByDomainId($route, array $routeParams, $domainId, $absolute = true)
    {
        $domainRouter = $this->domainRouterFactory->getRouter($domainId);

        try {
            return $domainRouter->generate($route, $routeParams, $absolute);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'router_extension';
    }
}
