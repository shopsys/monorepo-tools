<?php

namespace SS6\ShopBundle\Model\Customer;

class CustomerIdentifier {
	
	/**
	 * @var string 
	 */
	private $sessionId;
	
	/**
	 * @param string $sessionId
	 */
	public function __construct($sessionId) {
		$this->sessionId = $sessionId;
	}

	/**
	 * @return string
	 */
	public function getSessionId() {
		return $this->sessionId;
	}
}
