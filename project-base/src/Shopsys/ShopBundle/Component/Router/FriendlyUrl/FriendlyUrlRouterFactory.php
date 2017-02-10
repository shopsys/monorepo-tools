<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlGenerator;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlMatcher;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCompiler;

class FriendlyUrlRouterFactory
{

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader
     */
    private $delegatingLoader;

    /**
     * @var \Symfony\Component\Routing\RouteCompiler
     */
    private $routeCompiler;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRepository
     */
    private $friendlyUrlRepository;

    /**
     * @var string
     */
    private $friendlyUrlRouterResourceFilepath;

    public function __construct(
        $friendlyUrlRouterResourceFilepath,
        DelegatingLoader $delegatingLoader,
        RouteCompiler $routeCompiler,
        FriendlyUrlRepository $friendlyUrlRepository
    ) {
        $this->friendlyUrlRouterResourceFilepath = $friendlyUrlRouterResourceFilepath;
        $this->delegatingLoader = $delegatingLoader;
        $this->routeCompiler = $routeCompiler;
        $this->friendlyUrlRepository = $friendlyUrlRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Symfony\Component\Routing\RequestContext $context
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter
     */
    public function createRouter(DomainConfig $domainConfig, RequestContext $context) {
        return new FriendlyUrlRouter(
            $context,
            $this->delegatingLoader,
            new FriendlyUrlGenerator($context, $this->routeCompiler, $this->friendlyUrlRepository),
            new FriendlyUrlMatcher($this->friendlyUrlRepository),
            $domainConfig,
            $this->friendlyUrlRouterResourceFilepath
        );
    }
}
