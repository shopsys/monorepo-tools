<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture\Exception;

use Exception;

class EntityIdIsNotSetException extends Exception implements DataFixtureException
{
    /**
     * @param string $referenceName
     * @param object $object
     * @param \Exception|null $previous
     */
    public function __construct($referenceName, $object, Exception $previous = null)
    {
        $message = 'Cannot create persistent reference "' . $referenceName . '" for entity without ID. '
            . 'Flush the entity ("' . get_class($object) . '") before creating a persistent reference.';

        parent::__construct($message, 0, $previous);
    }
}
