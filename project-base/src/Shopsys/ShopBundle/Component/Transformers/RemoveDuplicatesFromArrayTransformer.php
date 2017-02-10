<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class RemoveDuplicatesFromArrayTransformer implements DataTransformerInterface {

    /**
     * @param mixed $values
     * @return mixed
     */
    public function transform($values) {
        return $values;
    }

    /**
     * @param array|null $array
     * @return array|null
     */
    public function reverseTransform($array) {
        if (is_array($array)) {
            $result = [];
            foreach ($array as $key => $value) {
                if (!in_array($value, $result, true)) {
                    $result[$key] = $value;
                }
            }

            return $result;
        }

        return $array;
    }
}
