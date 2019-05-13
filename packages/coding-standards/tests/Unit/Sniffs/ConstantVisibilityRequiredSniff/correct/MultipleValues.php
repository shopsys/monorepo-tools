<?php

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Correct;

class MultipleValues
{
    public const A = 'value';
    protected const B = 'value';
    private const C = 'value';
}
