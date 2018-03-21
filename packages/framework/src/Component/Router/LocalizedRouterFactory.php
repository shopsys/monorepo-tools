<?php

namespace Shopsys\FrameworkBundle\Component\Router;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class LocalizedRouterFactory
{
    /**
     * @var \Symfony\Component\Config\Loader\LoaderInterface
     */
    private $configLoader;

    /**
     * @var string[]
     */
    private $localeRoutersResourcesFilepaths;

    /**
     * @var \Symfony\Component\Routing\Router[][]
     */
    private $routersByLocaleAndHost;

    public function __construct($localeRoutersResourcesFilepaths, LoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
        $this->localeRoutersResourcesFilepaths = $localeRoutersResourcesFilepaths;
        $this->routersByLocaleAndHost = [];
    }

    /**
     * @param string $locale
     * @param \Symfony\Component\Routing\RequestContext $context
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter($locale, RequestContext $context)
    {
        if (!array_key_exists($locale, $this->localeRoutersResourcesFilepaths)) {
            $message = 'File with localized routes "routing_front_' . $locale . '.yml" was not found. '
                . 'Please add it to Resources/config folder.';
            throw new \Shopsys\FrameworkBundle\Component\Router\Exception\LocalizedRoutingConfigFileNotFoundException($message);
        }

        if (!array_key_exists($locale, $this->routersByLocaleAndHost)
            || !array_key_exists($context->getHost(), $this->routersByLocaleAndHost[$locale])
        ) {
            $this->routersByLocaleAndHost[$locale][$context->getHost()] = new Router(
                $this->configLoader,
                $this->localeRoutersResourcesFilepaths[$locale],
                [],
                $context
            );
        }

        return $this->routersByLocaleAndHost[$locale][$context->getHost()];
    }
}
