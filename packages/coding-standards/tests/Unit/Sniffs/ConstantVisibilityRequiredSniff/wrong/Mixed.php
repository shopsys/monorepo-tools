<?php

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Wrong;

class Mixed
{
    const A = 'value';
    public const B = 'value';
    /**
     * @access private
     */
    const C = 'value';
}
