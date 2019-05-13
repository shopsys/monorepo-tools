<?php

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Wrong;

class MixedAtTheEnd
{
    public const B = 'value';
    /**
     * @access private
     */
    const C = 'value';
    const A = 'value';
}
