<?php

namespace SS6\ShopBundle\Model\Administrator\Exception;

use Exception;

class InvalidGridLimitValueException extends Exception implements AdministratorException {

	/**
	 * @var mixed
	 */
	private $limit;

	/**
	 * @param mixed $limit
	 * @param Exception $previous
	 */
	public function __construct($limit, $previous = null) {
		parent::__construct('Administrator grid limit value ' . var_export($limit, true) . ' is invalid', 0, $previous);
	}

	/**
	 * @return mixed
	 */
	public function getLimit() {
		return $this->limit;
	}

}
