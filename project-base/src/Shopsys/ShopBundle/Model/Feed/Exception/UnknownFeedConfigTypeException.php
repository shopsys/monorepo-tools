<?php

namespace Shopsys\ShopBundle\Model\Feed\Exception;

use Exception;
use Shopsys\ShopBundle\DependencyInjection\Compiler\RegisterProductFeedConfigsCompilerPass;
use Shopsys\ShopBundle\Model\Feed\Exception\FeedException;

class UnknownFeedConfigTypeException extends Exception implements FeedException
{
    /**
     * @param string $serviceId
     * @param int $type
     * @param string[] $knownTypes
     * @param \Exception|null $previous
     */
    public function __construct($serviceId, $type, array $knownTypes, Exception $previous = null)
    {
        $message = sprintf(
            'Tried to register "%s" as a product feed of an unknown type "%s". Allowed types are: %s.',
            $serviceId,
            $type,
            implode(', ', $knownTypes)
        );

        parent::__construct($message, 0, $previous);
    }
}
