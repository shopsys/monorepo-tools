<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Unit\CsFixer\Phpdoc\NoUselessAccessFixer;

class Simple
{
    /**
     * @access public
     */
    public const A = 'value';
    /** @access protected */
    protected const B = 'value';
}
