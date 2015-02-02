<?php

namespace SS6\ShopBundle\Model\AdvanceSearch\Exception;

use Exception;
use SS6\ShopBundle\Model\AdvanceSearch\Exception\AdvanceSearchException;

class AdvanceSearchFilterNotFoundException extends Exception implements AdvanceSearchException {

	public function __construct($message, Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
