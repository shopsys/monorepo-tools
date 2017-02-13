<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class RemoveWhitespacesTransformer implements DataTransformerInterface
{
    /**
     * @param string|null $value
     * @return string|null
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    public function reverseTransform($value)
    {
        return $value === null ? null : preg_replace('/\s/', '', $value);
    }
}
