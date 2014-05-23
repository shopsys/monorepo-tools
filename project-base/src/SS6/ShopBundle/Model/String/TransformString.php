<?php

namespace SS6\ShopBundle\Model\String;

class TransformString {
	public static function safeFilename($string) {
		$string = preg_replace('~[^-\\.\\pL0-9_]+~u', '_', $string);
		$string = preg_replace('~[\\.]{2,}~u', '.', $string);
		$string = trim($string, '_');
		$string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
		$string = preg_replace('~[^-\\.a-zA-Z0-9_]+~', '', $string);
		$string = ltrim($string, '.');
		return $string;
	}
}
