<?php

namespace SS6\ShopBundle\Model\DataFixture\Exception;

use Exception;
use SS6\ShopBundle\Model\DataFixture\Exception\DataFixtureException;

class EntityNotFoundException extends Exception implements DataFixtureException {
	
	/**
	 * @param string $referenceName
	 * @param \Exception $previous
	 */
	public function __construct($referenceName, Exception $previous = null) {
		parent::__construct('Entity from reference  "' . $referenceName . '" not found.', 0, $previous);
	}
	
}
