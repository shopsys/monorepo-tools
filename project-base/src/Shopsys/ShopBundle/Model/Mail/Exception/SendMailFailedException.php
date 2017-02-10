<?php

namespace Shopsys\ShopBundle\Model\Mail\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Debug;

class SendMailFailedException extends Exception implements MailException
{

    /**
     * @var array
     */
    private $failedRecipients;

    /**
     * @param array $failedRecipients
     * @param \Exception|null $previous
     */
    public function __construct(array $failedRecipients, Exception $previous = null) {
        $this->failedRecipients = $failedRecipients;
        parent::__construct('Order mail was not send to ' . Debug::export($failedRecipients), 0, $previous);
    }

    /**
     * @return array
     */
    public function getFailedRecipients() {
        return $this->failedRecipients;
    }
}
