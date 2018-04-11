<?php

namespace Shopsys\FrameworkBundle\Component\Environment;

class EnvironmentType
{
    const DEVELOPMENT = 'dev';
    const PRODUCTION = 'prod';
    const TEST = 'test';

    /**
     * @param string $environment
     * @return bool
     */
    public static function isDebug(string $environment): bool
    {
        return $environment === self::DEVELOPMENT;
    }
}
