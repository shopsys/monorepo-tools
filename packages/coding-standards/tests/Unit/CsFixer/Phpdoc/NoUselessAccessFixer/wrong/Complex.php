<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class Complex
{
    public const A = 'value';
    /**
     * @access private
     * @internal
     */
    private const B = 'value';
    /**
     * @access protected
     * @internal
     */
    const C = 'value';
    /**
     * @access private
     */
    private const D = 'value';
    /** @access private */
    private const E = 'value';
    /** @access protected */
    const F = 'value';

    /**
     * @access public
     */
    public function methodA(): void
    {

    }

    /**
     * @access protected
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
