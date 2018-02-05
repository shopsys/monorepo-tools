<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader;
use Symfony\Component\Routing\RequestContext;

class FriendlyUrlRouterFactory
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader
     */
    private $delegatingLoader;

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
        FriendlyUrlRepository $friendlyUrlRepository
    ) {
        $this->friendlyUrlRouterResourceFilepath = $friendlyUrlRouterResourceFilepath;
        $this->delegatingLoader = $delegatingLoader;
        $this->friendlyUrlRepository = $friendlyUrlRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Symfony\Component\Routing\RequestContext $context
     * @return \Shopsys\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlRouter
     */
    public function createRouter(DomainConfig $domainConfig, RequestContext $context)
    {
        return new FriendlyUrlRouter(
            $context,
            $this->delegatingLoader,
            new FriendlyUrlGenerator($context, $this->friendlyUrlRepository),
            new FriendlyUrlMatcher($this->friendlyUrlRepository),
            $domainConfig,
            $this->friendlyUrlRouterResourceFilepath
        );
    }
}
