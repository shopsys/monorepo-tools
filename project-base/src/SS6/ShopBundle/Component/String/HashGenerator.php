<?php

namespace SS6\ShopBundle\Component\String;

class HashGenerator {

	const MIN = 100000;
	const MAX = 9999999;

	/**
	 * @return string
	 */
	public function getHash() {
		return uniqid(mt_rand(self::MIN, self::MAX));
	}
}
