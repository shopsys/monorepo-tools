<?php

namespace Tests\CodingStandards\Sniffs\ConstantVisibilityRequiredSniff\Wrong;

class MixedInTheMiddle
{
    /** @access private */
    public const OPTION_MODULE = 'module';

    const OPTION_LIST = 'list';

    private const OPTION_INSTANCE_NAME = 'instance-name';
}
