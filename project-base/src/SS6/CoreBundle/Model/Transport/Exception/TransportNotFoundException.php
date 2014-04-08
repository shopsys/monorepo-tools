<?php

namespace SS6\CoreBundle\Model\Transport\Exception;

use Exception;

class TransportNotFoundException extends Exception implements TransportException {
	
	public function __construct($criteria) {
		parent::__construct('Transport not found by criteria ' . var_export($criteria, true), 0, null);
	}
	
}
