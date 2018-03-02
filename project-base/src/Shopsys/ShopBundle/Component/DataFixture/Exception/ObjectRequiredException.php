<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture\Exception;

use Exception;
use InvalidArgumentException;
use Shopsys\FrameworkBundle\Component\Debug;

class ObjectRequiredException extends InvalidArgumentException implements DataFixtureException
{
    /**
     * @param mixed $given
     * @param \Exception|null $previous
     */
    public function __construct($given, Exception $previous = null)
    {
        parent::__construct('Object required, but given "' . Debug::export($given) . '"', 0, $previous);
    }
}
