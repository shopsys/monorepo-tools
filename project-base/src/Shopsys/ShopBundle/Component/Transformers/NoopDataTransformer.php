<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class NoopDataTransformer implements DataTransformerInterface
{

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value) {
        return $value;
    }
}
