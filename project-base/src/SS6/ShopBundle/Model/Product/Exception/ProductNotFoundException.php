<?php

namespace SS6\ShopBundle\Model\Product\Exception;

use Exception;

class ProductNotFoundException extends Exception implements ProductException {
	
	public function __construct($criteria) {
		parent::__construct('Product not found by criteria ' . var_export($criteria, true), 0, null);
	}
	
}
