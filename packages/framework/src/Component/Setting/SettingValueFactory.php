<?php

namespace Shopsys\FrameworkBundle\Component\Setting;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class SettingValueFactory implements SettingValueFactoryInterface
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
     * @param \DateTime|string|int|float|bool|null $value
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Component\Setting\SettingValue
     */
    public function create(
        string $name,
        $value,
        int $domainId
    ): SettingValue {
        $classData = $this->entityNameResolver->resolve(SettingValue::class);

        return new $classData($name, $value, $domainId);
    }
}
