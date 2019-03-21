<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class EmptyAccessAnnotation
{
    /**
     * @access
     */
    const A = 'value';
    /**
     * @access private
     */
    public const B = 'value';
}
