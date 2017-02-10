<?php

namespace Shopsys\ShopBundle\Component\Router;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class CurrentDomainRouter implements RouterInterface
{

    /**
     * @var \Symfony\Component\Routing\RequestContext
     */
    private $context;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    public function __construct(Domain $domain, DomainRouterFactory $domainRouterFactory) {
        $this->domain = $domain;
        $this->domainRouterFactory = $domainRouterFactory;
    }

    /**
     * @return \Symfony\Component\Routing\RequestContext
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     */
    public function setContext(RequestContext $context) {
        $this->context = $context;
    }

    /**
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function getRouteCollection() {
        return $this->getDomainRouter()->getRouteCollection();
    }

    /**
     * @param string $routeName
     * @param array $parameters
     * @param bool $referenceType
     * @return string
     */
    public function generate($routeName, $parameters = [], $referenceType = self::ABSOLUTE_PATH) {
        return $this->getDomainRouter()->generate($routeName, $parameters, $referenceType);
    }

    /**
     * @param string $pathinfo
     * @return array
     */
    public function match($pathinfo) {
        return $this->getDomainRouter()->match($pathinfo);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Router\DomainRouter
     */
    private function getDomainRouter() {
        return $this->domainRouterFactory->getRouter($this->domain->getid());
    }
}
