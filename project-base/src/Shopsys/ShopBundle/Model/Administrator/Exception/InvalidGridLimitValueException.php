<?php

namespace Shopsys\ShopBundle\Model\Administrator\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Debug;

class InvalidGridLimitValueException extends Exception implements AdministratorException
{

    /**
     * @var mixed
     */
    private $limit;

    /**
     * @param mixed $limit
     * @param \Exception|null $previous
     */
    public function __construct($limit, Exception $previous = null) {
        parent::__construct('Administrator grid limit value ' . Debug::export($limit) . ' is invalid', 0, $previous);
    }

    /**
     * @return mixed
     */
    public function getLimit() {
        return $this->limit;
    }
}
