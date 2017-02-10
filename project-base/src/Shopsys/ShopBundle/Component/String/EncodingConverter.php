<?php

namespace Shopsys\ShopBundle\Component\String;

class EncodingConverter {

	/**
	 * @param string $stringCp1250
	 * @return string
	 */
	private static function stringCp1250ToUtf8($stringCp1250) {
		return iconv('CP1250', 'UTF-8//TRANSLIT', $stringCp1250);
	}

	/**
	 * @param array $array
	 * @return array
	 */
	private static function arrayCp1250ToUtf8(array $array) {
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = self::arrayCp1250ToUtf8($value);
			} elseif (is_string($value)) {
				$array[$key] = self::stringCp1250ToUtf8($value);
			}
		}

		return $array;
	}

	/**
	 * @param mixed $value
	 * @return mixed
	 */
	public static function cp1250ToUtf8($value) {
		if (is_array($value)) {
			$value = self::arrayCp1250ToUtf8($value);
		} elseif (is_string($value)) {
			$value = self::stringCp1250ToUtf8($value);
		}

		return $value;
	}
}
