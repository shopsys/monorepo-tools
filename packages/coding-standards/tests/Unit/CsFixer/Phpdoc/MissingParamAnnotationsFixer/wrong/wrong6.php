<?php

namespace Shopsys\CodingStandards\Tests;

use Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming;

final class ObjectIsCreatedByFactorySniff
{
    /**
     * @var \Symplify\TokenRunner\Analyzer\SnifferAnalyzer\Naming
     */
    private $naming;

    public function __construct(Naming $naming)
    {
    }
}
