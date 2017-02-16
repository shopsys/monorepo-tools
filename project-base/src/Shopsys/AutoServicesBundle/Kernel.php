<?php

namespace Shopsys\AutoServicesBundle;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;

abstract class Kernel extends BaseKernel
{
    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        $parentContainer = parent::getContainer();

        if ($parentContainer !== null) {
            return $parentContainer->get('shopsys.auto_services.auto_container');
        } else {
            return null;
        }
    }

    protected function initializeContainer()
    {
        if ($this->isContainerClassAlreadyLoaded()) {
            $this->initializeContainerWithoutRebuilding();
        } else {
            parent::initializeContainer();
        }
    }

    private function initializeContainerWithoutRebuilding()
    {
        $class = $this->getContainerClass();
        $this->container = new $class();
        $this->container->set('kernel', $this);
    }

    /**
     * @return bool
     */
    private function isContainerClassAlreadyLoaded()
    {
        $class = $this->getContainerClass();
        return class_exists($class, false);
    }
}
