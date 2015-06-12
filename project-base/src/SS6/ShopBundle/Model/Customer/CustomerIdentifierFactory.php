<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use Symfony\Component\HttpFoundation\Session\Session;

class CustomerIdentifierFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	public function __construct(CurrentCustomer $currentCustomer, Session $session) {
		$this->currentCustomer = $currentCustomer;
		$this->session = $session;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\CustomerIdentifier
	 */
	public function get() {
		$sessionId = $this->session->getId();

		// when session is not started, returning empty string is behaviour of session_id()
		if ($sessionId === '') {
			$this->session->start();
			$sessionId = $this->session->getId();
		}

		$customerIdentifier = new CustomerIdentifier($sessionId, $this->currentCustomer->findCurrentUser());

		return $customerIdentifier;
	}

	/**
	 * @param string $sessionId
	 * @return \SS6\ShopBundle\Model\Customer\CustomerIdentifier
	 */
	public function getOnlyWithSessionId($sessionId) {
		$customerIdentifier = new CustomerIdentifier($sessionId, null);

		return $customerIdentifier;
	}
}
