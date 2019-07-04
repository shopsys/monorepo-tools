<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\ArrayUtils;

class RecursiveArraySorter
{
    /**
     * @param array $array
     * @return bool
     */
    public function recursiveArrayKsort(array &$array): bool
    {
        $return = true;
        foreach ($array as &$value) {
            if (is_array($value)) {
                $return = $this->recursiveArrayKsort($value) && $return;
            }
        }
        return ksort($array) && $return;
    }
}
