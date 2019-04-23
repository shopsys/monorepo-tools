<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class Complex
{
    public const A = 'value';
    /**
     * @internal
     */
    private const B = 'value';
    /**
     * @access protected
     * @internal
     */
    const C = 'value';
    
    private const D = 'value';
    
    private const E = 'value';
    /** @access protected */
    const F = 'value';

    
    public function methodA(): void
    {

    }

    /**
     * @param string $argumentA
     * @param string $argumentB
     * @return bool
     */
    protected function methodB(string $argumentA, string $argumentB): bool
    {
        return true;
    }

    /**
     * @param string $argumentA
     * @param string $argumentB
     * @return bool
     */
    protected function methodC(string $argumentA, string $argumentB): bool
    {
        return true;
    }
}
