<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;

class CustomerIdentifierFactory {

	/**
	 * @var \Symfony\Component\Security\Core\SecurityContext
	 */
	private $user;

	/**
	 *
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	/**
	 * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
	 * @param \Symfony\Component\HttpFoundation\Session\Session $session
	 */
	public function __construct(SecurityContext $securityContext, Session $session) {
		$this->session = $session;

		$token = $securityContext->getToken();
		if ($token !== null) {
			$user = $token->getUser();
			if ($user instanceof User) {
				$this->user = $user;
			}
		}
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

		$customerIdentifier = new CustomerIdentifier($this->session->getId(), $this->user);

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
