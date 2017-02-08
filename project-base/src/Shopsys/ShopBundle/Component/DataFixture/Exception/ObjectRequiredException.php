<?php

namespace SS6\ShopBundle\Component\DataFixture\Exception;

use Exception;
use InvalidArgumentException;
use SS6\ShopBundle\Component\DataFixture\Exception\DataFixtureException;
use SS6\ShopBundle\Component\Debug;

class ObjectRequiredException extends InvalidArgumentException implements DataFixtureException {

	/**
	 * @param mixed $given
	 * @param \Exception|null $previous
	 */
	public function __construct($given, Exception $previous = null) {
		parent::__construct('Object required, but given "' . Debug::export($given, true) . '"', 0, $previous);
	}

}
