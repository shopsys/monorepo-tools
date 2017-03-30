<?php

namespace Shopsys\IntegrationTestingBundle\ServiceLocator\Exception;

use Exception;

class AmbiguousTypeNameException extends Exception
{
    /**
     * @param string $typeName
     * @param string[] $matchingServiceIds
     */
    public function __construct($typeName, $matchingServiceIds)
    {
        $message = sprintf(
            'Service cannot be located by type because type "%s" is ambiguous.'
                . ' It matches following service definitions: "%s".',
            $typeName,
            implode('", "', $matchingServiceIds)
        );

        parent::__construct($message, 0, null);
    }
}
