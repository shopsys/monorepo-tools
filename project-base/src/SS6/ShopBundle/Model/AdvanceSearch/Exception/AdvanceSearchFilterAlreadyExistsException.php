<?php

namespace SS6\ShopBundle\Model\AdvanceSearch\Exception;

use Exception;
use SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchException;

class AdvanceSearchFilterAlreadyExistsException extends Exception implements AdvanceSearchException {

	public function __construct($message, $previous) {
		parent::__construct($message, 0, $previous);
	}

}
