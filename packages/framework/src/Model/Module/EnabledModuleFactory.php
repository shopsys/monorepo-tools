<?php

namespace Shopsys\FrameworkBundle\Model\Module;

class EnabledModuleFactory implements EnabledModuleFactoryInterface
{
    /**
     * @param string $name
     * @return \Shopsys\FrameworkBundle\Model\Module\EnabledModule
     */
    public function create(string $name): EnabledModule
    {
        return new EnabledModule($name);
    }
}
