<?php

namespace Shopsys\ShopBundle\Model\Feed\Exception;

use Exception;
use Shopsys\ShopBundle\DependencyInjection\Compiler\RegisterProductFeedConfigsCompilerPass;
use Shopsys\ShopBundle\Model\Feed\Exception\FeedException;

class UnknownFeedConfigTypeException extends Exception implements FeedException
{
    /**
     * @param int $type
     * @param string[] $knownTypes
     * @param \Exception|null $previous
     */
    public function __construct($type, array $knownTypes, Exception $previous = null)
    {
        $message = sprintf(
            'Trying to register feed config of an unknown type "%s". Allowed types are: %s.',
            $type,
            implode(', ', $knownTypes)
        );

        parent::__construct($message, 0, $previous);
    }
}
