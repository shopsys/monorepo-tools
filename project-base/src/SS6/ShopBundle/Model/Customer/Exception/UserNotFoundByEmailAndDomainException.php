<?php

namespace SS6\ShopBundle\Model\Customer\Exception;

use Exception;

class UserNotFoundByEmailAndDomainException extends UserNotFoundException {

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var int
	 */
	private $domainId;

	/**
	 * @param mixed $criteria
	 * @param \Exception $previous
	 */
	public function __construct($email, $domainId, Exception $previous = null) {
		parent::__construct([
			'email' => $email,
			'domainId' => $domainId,
		]);

		$this->email = $email;
		$this->domainId = $domainId;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

}
