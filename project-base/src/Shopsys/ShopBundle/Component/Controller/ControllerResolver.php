<?php

namespace Shopsys\ShopBundle\Component\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver as SymfonyControllerResolver;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class ControllerResolver extends SymfonyControllerResolver
{
    /**
     * {@inheritDoc}
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count) {
                // controller in the a:b:c notation then
                $controller = $this->parser->parse($controller);
            } elseif (1 == $count) {
                // controller in the service:method notation
                list($service, $method) = explode(':', $controller, 2);

                return [$this->container->get($service), $method];
            } else {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }

        list($class, $method) = explode('::', $controller, 2);

        $controller = $this->container->get($class);

        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }

        return [$controller, $method];
    }
}
