<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ForceLateStaticBindingForProtectedConstantsSniff;

class SingleValue
{
    public const A = 'value';
    protected const B = 'value';
    private const C = 'value';
    /** @access public */
    const D = 'value';
    /**
     * @access protected
     */
    const E = 'value';
    /** @access private */
    const F = 'value';
    const G = 'value';

    public function method()
    {
        echo self::A;
        echo static::B;
        echo self::C;
        echo self::D;
        echo static::E;
        echo self::F;
        echo self::G;

        echo static::A;
        echo static::B;
        echo static::C;
        echo static::D;
        echo static::E;
        echo static::F;
        echo static::G;
    }
}
