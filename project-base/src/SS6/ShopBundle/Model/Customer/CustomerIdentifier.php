<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\User;

class CustomerIdentifier {
	
	/**
	 * @var string 
	 */
	private $sessionId;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\User|null
	 */
	private $user;
	
	/**
	 * @param string $sessionId
	 */
	public function __construct($sessionId, User $user = null) {
		$this->sessionId = $sessionId;
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getSessionId() {
		return $this->sessionId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\User|null
	 */
	public function getUser() {
		return $this->user;
	}

}
