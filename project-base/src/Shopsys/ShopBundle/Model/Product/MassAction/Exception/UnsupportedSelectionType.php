<?php

namespace Shopsys\ShopBundle\Model\Product\MassAction\Exception;

use Exception;

class UnsupportedSelectionType extends Exception implements MassActionException {

    /**
     * @param string $selectionType
     * @param \Exception|null $previous
     */
    public function __construct($selectionType, Exception $previous = null) {
        parent::__construct(sprintf('Selection type "%s" is not supported', $selectionType), 0, $previous);
    }

}
