<?php

namespace Shopsys\ShopBundle\Model\Product\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Product\Exception\VariantException;

class VariantCanBeAddedOnlyToMainVariantException extends Exception implements VariantException
{

    /**
     * @param int $productId
     * @param int $variantId
     * @param \Exception|null $previous
     */
    public function __construct($productId, $variantId, Exception $previous = null) {
        $message = 'Product with ID ' . $productId . ' is not main variant for add variant ID ' . $variantId . '.';
        parent::__construct($message, 0, $previous);
    }

}
