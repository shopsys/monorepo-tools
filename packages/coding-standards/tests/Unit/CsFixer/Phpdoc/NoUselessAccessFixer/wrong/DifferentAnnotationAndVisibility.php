<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class DifferentAnnotationAndVisibility
{
    /** @access protected */
    public const A = 'value';
    /** @access public */
    private const B = 'value';
    /** @access private */
    protected const C = 'value';
}
