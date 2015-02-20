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

	/**
	 * @param string $string
	 * @return string
	 * @link http://php.vrana.cz/vytvoreni-pratelskeho-url.php
	 */
	public static function stringToFriendlyUrl($string) {
		$url = $string;
		$url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
		$url = trim($url, '-');
		$url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
		$url = strtolower($url);
		$url = preg_replace('~[^-a-z0-9_]+~', '', $url);

		return $url;
	}
}
