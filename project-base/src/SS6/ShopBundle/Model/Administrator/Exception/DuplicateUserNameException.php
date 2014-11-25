<?php

namespace SS6\ShopBundle\Model\Administrator\Exception;

use Exception;

class DuplicateUserNameException extends Exception implements AdministratorException {

	/**
	 * @var string
	 */
	private $userName;

	/**
	 * @param string $userName
	 * @param Exception $previous
	 */
	public function __construct($userName, $previous = null) {
		$this->userName = $userName;

		parent::__construct('Administrator with user name ' . $this->userName . ' already exists.', 0, $previous);
	}
}
