<?php

declare(strict_types=1);

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Correct;

class Annotation
{
    /** @access private */
    const A = 'value';
    /**
     * @access private
     */
    const B = 'value';
    /**
     * @access public
     */
    const C = 'value';
    /**
     * @access protected
     */
    const D = 'value';
    /**
     * @access private
     */
    const E = 'value';
    /**
     * @access private
     * @deprecated
     */
    const F = 'value';
    /**
     * @deprecated
     * @access private
     */
    const G = 'value';
}
