<?php

namespace Shopsys\ShopBundle\Component\String;

class HashGenerator
{
    private $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

    /**
     * @param int $length
     * @return string
     */
    public function generateHash($length)
    {
        $numberOfChars = strlen($this->characters);

        $hash = '';
        for ($i = 1; $i <= $length; $i++) {
            $randomIndex = random_int(0, $numberOfChars - 1);
            $hash .= $this->characters[$randomIndex];
        }

        return $hash;
    }
}
