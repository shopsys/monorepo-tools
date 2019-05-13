<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Correct;

class MixedVisibilities
{
    /** @access private */
    protected const A = 'value';
    /** @access private */
    public const B = 'value';
    /** @access public */
    protected const C = 'value';
    /** @access public */
    private const D = 'value';
    /** @access protected */
    private const E = 'value';
    /** @access protected */
    public const F = 'value';
}
