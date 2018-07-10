<?php

namespace Shopsys\FrameworkBundle\Model\Feed\Exception;

use Exception;

class UnknownFeedTypeException extends Exception implements FeedException
{
    /**
     * @param string $type
     * @param string[] $knownTypes
     * @param \Exception|null $previous
     */
    public function __construct(string $type, array $knownTypes, Exception $previous = null)
    {
        $message = sprintf(
            'Trying to register or access a feed of an unknown type "%s". Allowed types are: %s.',
            $type,
            implode(', ', $knownTypes)
        );

        parent::__construct($message, 0, $previous);
    }
}
