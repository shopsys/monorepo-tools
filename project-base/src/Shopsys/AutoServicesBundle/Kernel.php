<?php

namespace Shopsys\AutoServicesBundle;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;

abstract class Kernel extends BaseKernel
{
    /**
     * {@inheritdoc}
     */
    public function getContainer() {
        return parent::getContainer()->get('shopsys.auto_services.auto_container');
    }

    protected function initializeContainer() {
        if ($this->isContainerClassAlreadyLoaded()) {
            $this->initializeContainerWithoutRebuilding();
        } else {
            parent::initializeContainer();
        }
    }

    private function initializeContainerWithoutRebuilding() {
        $class = $this->getContainerClass();
        $this->container = new $class();
        $this->container->set('kernel', $this);
    }

    /**
     * @return bool
     */
    private function isContainerClassAlreadyLoaded() {
        $class = $this->getContainerClass();
        return class_exists($class, false);
    }
}
