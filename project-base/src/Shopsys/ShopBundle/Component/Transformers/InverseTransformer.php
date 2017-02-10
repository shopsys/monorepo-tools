<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class InverseTransformer implements DataTransformerInterface {

    /**
     * @param bool $value
     * @return bool
     */
    public function transform($value) {

        return !$value;
    }

    /**
     * @param bool $value
     * @return bool
     */
    public function reverseTransform($value) {
        return !$value;
    }
}
