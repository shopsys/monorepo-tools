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

	/**
	 * @param array $array
	 * @param string|int $key
	 * @param mixed $defaultValue
	 */
	public static function setArrayDefaultValue(&$array, $key, $defaultValue = null) {
		if (!array_key_exists($key, $array)) {
			$array[$key] = $defaultValue;
		}
	}
}
