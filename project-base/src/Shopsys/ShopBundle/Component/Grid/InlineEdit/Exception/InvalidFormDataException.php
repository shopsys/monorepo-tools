<?php

namespace Shopsys\ShopBundle\Component\Grid\InlineEdit\Exception;

use Exception;

class InvalidFormDataException extends Exception implements InlineEditException
{

    /**
     * @var array
     */
    private $formErrors;

    /**
     * @param array $formErrors
     * @param \Exception|null $previous
     */
    public function __construct(array $formErrors, Exception $previous = null) {
        $this->formErrors = $formErrors;
        parent::__construct('Inline edit form is not valid', 0, $previous);
    }

    /**
     * @return array
     */
    public function getFormErrors() {
        return $this->formErrors;
    }

}
