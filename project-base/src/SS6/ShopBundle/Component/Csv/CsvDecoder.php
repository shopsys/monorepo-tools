<?php

namespace SS6\ShopBundle\Component\Csv;

class CsvDecoder {

	/**
	 * @param string $value
	 * @return boolean
	 */
	public static function decodeBoolean($value) {
		return $value === 'true';
	}

}
