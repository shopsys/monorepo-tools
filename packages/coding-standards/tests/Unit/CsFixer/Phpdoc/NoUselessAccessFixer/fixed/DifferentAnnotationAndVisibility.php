<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class DifferentAnnotationAndVisibility
{
    
    public const A = 'value';
    
    private const B = 'value';
    
    protected const C = 'value';
}
