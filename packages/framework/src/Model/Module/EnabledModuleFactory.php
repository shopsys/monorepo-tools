<?php

namespace Shopsys\FrameworkBundle\Model\Module;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class EnabledModuleFactory implements EnabledModuleFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param string $name
     * @return \Shopsys\FrameworkBundle\Model\Module\EnabledModule
     */
    public function create(string $name): EnabledModule
    {
        $classData = $this->entityNameResolver->resolve(EnabledModule::class);

        return new $classData($name);
    }
}
