<?php

namespace Shopsys\ShopBundle\Component\String;

class HashGenerator
{
    private $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * @param int $length
     * @return string
     */
    public function generateHash($length) {
        $numberOfChars = strlen($this->characters);

        $hash = '';
        for ($i = 1; $i <= $length; $i++) {
            $randomIndex = $this->getRandomUnsigned16($numberOfChars - 1);
            $hash .= $this->characters[$randomIndex];
        }

        return $hash;
    }

    /**
     * @param int $max
     */
    private function getRandomUnsigned16($max) {
        do {
            $result = $this->getRandomUnsigned16PossiblyOutOfRange($max);
        } while ($result > $max);

        return $result;
    }

    /**
     * @param int $max
     * @return int
     */
    private function getRandomUnsigned16PossiblyOutOfRange($max) {
        $iv = mcrypt_create_iv(2, MCRYPT_DEV_URANDOM);
        if ($iv === false) {
            throw new \Shopsys\ShopBundle\Component\String\Exception\HashGenerationFailedException();
        }

        $unpacked16 = unpack('v', $iv);
        $randomUnsigned16 = $unpacked16[1];

        $numberOfBits = $this->getNumberOfBits($max);
        $bitMask = $this->getBitMask($numberOfBits);

        return $randomUnsigned16 & $bitMask;
    }

    /**
     * @param int $number
     * @return int
     */
    private function getNumberOfBits($number) {
        $numberOfBits = 0;

        while ($number > 0) {
            $number = $number >> 1;
            $numberOfBits++;
        }

        return $numberOfBits;
    }

    /**
     * @param int $numberOfOnes
     * @return int
     */
    private function getBitMask($numberOfOnes) {
        return (1 << $numberOfOnes) - 1;
    }
}
