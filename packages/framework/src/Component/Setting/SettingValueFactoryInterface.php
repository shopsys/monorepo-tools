<?php

namespace Shopsys\FrameworkBundle\Component\Setting;

interface SettingValueFactoryInterface
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
    ): SettingValue;
}
