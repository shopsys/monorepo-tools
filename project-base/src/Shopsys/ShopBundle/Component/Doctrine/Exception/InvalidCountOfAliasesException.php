<?php

namespace Shopsys\ShopBundle\Component\Doctrine\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Debug;

class InvalidCountOfAliasesException extends Exception
{
    /**
     * @param array|null $rootAliases
     * @param \Exception|null $previous
     */
    public function __construct(array $rootAliases = null, Exception $previous = null) {
        parent::__construct('Query builder has invalid count of root aliases ' . Debug::export($rootAliases), 0, $previous);
    }
}
