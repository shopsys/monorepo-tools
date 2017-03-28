<?php

namespace Shopsys\IntegrationTestingBundle\ServiceLocator\Exception;

use Exception;

class UnknownTypeNameException extends Exception
{
    /**
     * @param string $typeName
     */
    public function __construct($typeName)
    {
        $message = sprintf(
            'There is no service with type "%s" among public and autowired services in the container.',
            $typeName
        );

        parent::__construct($message, 0, null);
    }
}
