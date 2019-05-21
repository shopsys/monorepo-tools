<?php

namespace Shopsys\FrameworkBundle\Model\Administrator\Exception;

use Exception;
use Shopsys\FrameworkBundle\Component\Utils\Debug;

class InvalidGridLimitValueException extends Exception implements AdministratorException
{
    /**
     * @var mixed
     */
    protected $limit;

    /**
     * @param mixed $limit
     * @param \Exception|null $previous
     */
    public function __construct($limit, ?Exception $previous = null)
    {
        parent::__construct('Administrator grid limit value ' . Debug::export($limit) . ' is invalid', 0, $previous);
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
