<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\User;

class CustomerIdentifier {
	
	/**
	 * @var string 
	 */
	private $sessionId = '';

	/**
	 * @var \SS6\ShopBundle\Model\Customer\User|null
	 */
	private $user;
	
	/**
	 * @param string $sessionId
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 */
	public function __construct($sessionId, User $user = null) {
		$this->user = $user;
		if ($this->user === null) {
			$this->sessionId = $sessionId;
		}
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

	/**
	 * @return string
	 */
	public function getObjectHash() {
		if ($this->user instanceof User) {
			$userId = $this->user->getId();
		} else {
			$userId = 'NULL';
		}
		return 'session:' . $this->sessionId . ';userId:' . $userId . ';';
	}

}
