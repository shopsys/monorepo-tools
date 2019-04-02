<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class CronModuleFactory implements CronModuleFactoryInterface
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
     * @param string $serviceId
     * @return \Shopsys\FrameworkBundle\Component\Cron\CronModule
     */
    public function create(string $serviceId): CronModule
    {
        $classData = $this->entityNameResolver->resolve(CronModule::class);

        return new $classData($serviceId);
    }
}
