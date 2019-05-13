<?php

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Wrong;

class MissingAnnotation
{
    /**
     * @deprecated
     */
    const A = 'value';
    /**
     * @access
     */
    const B = 'value';
}
