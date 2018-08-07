<?php

namespace Shopsys\FrameworkBundle\Component\Setting;

class SettingValueFactory implements SettingValueFactoryInterface
{
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
        return new SettingValue($name, $value, $domainId);
    }
}
