<?php

namespace SS6\ShopBundle\Component\String;

class TransformString {

	/**
	 * @param string $string
	 * @return string
	 */
	public static function safeFilename($string) {
		$string = preg_replace('~[^-\\.\\pL0-9_]+~u', '_', $string);
		$string = preg_replace('~[\\.]{2,}~u', '.', $string);
		$string = trim($string, '_');
		$string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
		$string = preg_replace('~[^-\\.a-zA-Z0-9_]+~', '', $string);
		$string = ltrim($string, '.');

		return $string;
	}

	/**
	 * @param string $value
	 * @return string|null
	 */
	public static function emptyToNull($value) {
		return $value === '' ? null : $value;
	}
}
