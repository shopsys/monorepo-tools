<?php

namespace SS6\ShopBundle\Model\Cart\Exception;

use Exception;
use SS6\ShopBundle\Model\Cart\Exception\CartException;

class InvalidQuantityException extends Exception implements CartException {

	/**
	 * @var mixed
	 */
	private $invalidValue;

	/**
	 * @param mixed $invalidValue
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($invalidValue, $message = '', Exception $previous = null) {
		$this->invalidValue = $invalidValue;
		parent::__construct($message, 0, $previous);
	}

	/**
	 * @return mixed
	 */
	public function getInvalidValue() {
		return $this->invalidValue;
	}
}
