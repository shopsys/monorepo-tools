<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Component\Security\Core\SecurityContext;

class CustomerIdentifierFactory {

	/**
	 * @var \Symfony\Component\Security\Core\SecurityContext
	 */
	private $user;

	/**
	 * @param \Symfony\Component\Security\Core\SecurityContext $securityContext
	 */
	public function __construct(SecurityContext $securityContext) {
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
		$customerIdentifier = new CustomerIdentifier(session_id(), $this->user);
		
		return $customerIdentifier;
	}
}
