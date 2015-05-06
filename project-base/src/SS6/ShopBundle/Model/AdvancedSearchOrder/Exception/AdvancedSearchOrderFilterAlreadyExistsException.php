<?php

namespace SS6\ShopBundle\Model\AdvancedSearchOrder\Exception;

use Exception;
use SS6\ShopBundle\Model\AdvancedSearchOrder\Exception\AdvancedSearchOrderException;

class AdvancedSearchOrderFilterAlreadyExistsException extends Exception implements AdvancedSearchOrderException {

	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
