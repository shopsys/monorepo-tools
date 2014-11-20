<?php

namespace SS6\ShopBundle\Model\Mail\Exception;

use Exception;

class SendMailFailedException extends Exception implements MailException {

	/**
	 * @var array
	 */
	private $failedRecipients;

	/**
	 * @param array $failedRecipients
	 * @param \Exception $previous
	 */
	public function __construct($failedRecipients, Exception $previous = null) {
		$this->failedRecipients = $failedRecipients;
		parent::__construct('Order mail was not send to ' . var_export($failedRecipients, true), 0, $previous);
	}

	/**
	 * @return array
	 */
	public function getFailedRecipients() {
		return $this->failedRecipients;
	}
}
