<?php

declare(strict_types=1);

namespace Shopsys\CodingStandards\Helper;

final class PhpdocRegex
{
    /**
     * This matches the "$value" part in:
     * "@param type $value"
     *
     * @var string
     */
    public const ARGUMENT_NAME_PATTERN = '#@param(?:.*?)(\$\w+)$#s';
}
