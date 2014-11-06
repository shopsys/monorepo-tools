<?php

namespace SS6\ShopBundle\Component\DataFixture\Exception;

use Exception;
use SS6\ShopBundle\Component\DataFixture\Exception\DataFixtureException;

class PersistentReferenceNotFoundException extends Exception implements DataFixtureException {

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($criteria, Exception $previous = null) {
		parent::__construct('Data fixture reference not found by criteria ' . var_export($criteria, true), 0, $previous);
	}

}
