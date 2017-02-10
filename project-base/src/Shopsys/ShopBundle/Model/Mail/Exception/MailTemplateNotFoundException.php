<?php

namespace Shopsys\ShopBundle\Model\Mail\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Mail\Exception\MailException;

class MailTemplateNotFoundException extends Exception implements MailException {

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
