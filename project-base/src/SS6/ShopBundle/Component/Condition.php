<?php

namespace SS6\ShopBundle\Component;

class Condition {

	/**
	 * @param mixed $testVariable
	 * @param mixed $default
	 * @return mixed
	 */
	public static function ifNull($testVariable, $default) {
		return $testVariable !== null ? $testVariable : $default;
	}
}
