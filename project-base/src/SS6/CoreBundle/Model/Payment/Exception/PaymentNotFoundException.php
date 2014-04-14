<?php

namespace SS6\CoreBundle\Model\Payment\Exception;

use Exception;

class PaymentNotFoundException extends Exception implements PaymentException {
	
	public function __construct($criteria, \Exception $previous = null) {
		parent::__construct('Payment not found by criteria ' . var_export($criteria, true), 0, $previous);
	}
	
}
