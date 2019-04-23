<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Correct;

class Mixed
{
    /** @access private */
    const A = 'value';
    public const B = 'value';
    protected const C = 'value';
    /**
     * @access protected
     */
    const D = 'value';
    private const E = 'value';
    /**
     * @access private
     * @deprecated
     */
    const F = 'value';
    /**
     * @deprecated
     */
    protected const G = 'value';
}
