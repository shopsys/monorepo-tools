<?php

namespace SS6\ShopBundle\Model\Csv;

class CsvDecoder {

	/**
	 * @param string $value
	 * @return boolean
	 */
	public static function decodeBoolean($value) {
		return $value === 'true';
	}

}
