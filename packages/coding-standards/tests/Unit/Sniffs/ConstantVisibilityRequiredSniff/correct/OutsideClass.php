<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Correct;

const B = 'value';

class OutsideClass
{
    public const A = 'value';
}

const A = 'value';
