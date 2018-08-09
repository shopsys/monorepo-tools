<?php

namespace Shopsys\FrameworkBundle\Model\Advert\Exception;

use Exception;

class AdvertPositionNotKnownException extends Exception implements AdvertException
{
    public function __construct(string $positionName, array $knownPositionsNames, Exception $previous = null)
    {
        $message = sprintf(
            'Unknown advert position name "%s". Known names are %s.',
            $positionName,
            implode('", "', $knownPositionsNames)
        );

        parent::__construct($message, 0, $previous);
    }
}
