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
		$customerIdentifier = new CustomerIdentifier($this->session->getId(), $this->user);
		
		return $customerIdentifier;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\CustomerIdentifier
	 */
	public function getOnlyWithSessionId($sessionId) {
		$customerIdentifier = new CustomerIdentifier($sessionId, null);

		return $customerIdentifier;
	}
}
