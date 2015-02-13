<?php

namespace SS6\ShopBundle\Component\String;

class HashGenerator {

	private $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

	/**
	 * @param int $length
	 * @return string
	 */
	public function generateHash($length) {
		$numberOfChars = strlen($this->characters);

		$hash = '';
		for ($i = 1; $i <= $length; $i++) {
			$randomIndex = $this->randomUnsigned32(0, $numberOfChars - 1);
			$hash .= $this->characters[$randomIndex];
		}

		return $hash;
	}

	/**
	 * @param int $min
	 * @param int $max
	 * @throws \SS6\ShopBundle\Component\String\Exception\HashGenerationFailedException
	 */
	private function randomUnsigned32($min, $max) {
		$iv = mcrypt_create_iv(4, MCRYPT_DEV_URANDOM);
		if ($iv === false) {
			throw new \SS6\ShopBundle\Component\String\Exception\HashGenerationFailedException();
		}

		$unpacked32 = unpack('V', $iv);
		$randomUnsigned32 = $unpacked32[1];

		$normalizedRandom = $randomUnsigned32 / 4294967295; // [0.0 - 1.0]

		$result = $min + ($max - $min) * $normalizedRandom;
		$roundedResult = (int)round($result);

		// don't let rounding get number out of range
		if ($roundedResult < $min) {
			$roundedResult = $min;
		} elseif ($roundedResult > $max) {
			$roundedResult = $max;
		}

		return $roundedResult;
	}

}
