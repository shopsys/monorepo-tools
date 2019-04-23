<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class EmptyAccessAnnotation
{
    
    const A = 'value';
    
    public const B = 'value';
}
