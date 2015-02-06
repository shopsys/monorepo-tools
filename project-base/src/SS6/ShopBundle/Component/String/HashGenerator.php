<?php

namespace SS6\ShopBundle\Component\String;

class HashGenerator {

	private $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

	public function generateHash($length) {
		$numberOfChars = strlen($this->characters);

		$iv = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
		if ($iv === false) {
			throw new \SS6\ShopBundle\Component\String\Exception\HashGenerationFailedException();
		}

		$randomChars = unpack('C*', $iv);

		$hash = '';
		for ($i = 1; $i <= $length; $i++) {
			$randomIndex = $randomChars[$i] % $numberOfChars;

			$hash .= $this->characters[$randomIndex];
		}

		return $hash;
	}

}
