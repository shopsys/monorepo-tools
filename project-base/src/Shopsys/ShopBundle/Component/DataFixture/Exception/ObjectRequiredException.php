<?php

namespace Shopsys\ShopBundle\Component\DataFixture\Exception;

use Exception;
use InvalidArgumentException;
use Shopsys\ShopBundle\Component\DataFixture\Exception\DataFixtureException;
use Shopsys\ShopBundle\Component\Debug;

class ObjectRequiredException extends InvalidArgumentException implements DataFixtureException {

    /**
     * @param mixed $given
     * @param \Exception|null $previous
     */
    public function __construct($given, Exception $previous = null) {
        parent::__construct('Object required, but given "' . Debug::export($given, true) . '"', 0, $previous);
    }

}
