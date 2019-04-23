<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class Simple
{
    
    public const A = 'value';
    
    protected const B = 'value';
}
