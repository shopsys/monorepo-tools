<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Customer\User;

class CustomerIdentifier {

	/**
	 * @var string
	 */
	private $cartIdentifier = '';

	/**
	 * @var \SS6\ShopBundle\Model\Customer\User|null
	 */
	private $user;

	/**
	 * @param string $cartIdentifier
	 * @param \SS6\ShopBundle\Model\Customer\User|null $user
	 */
	public function __construct($cartIdentifier, User $user = null) {
		if ($cartIdentifier === '' && $user === null) {
			$message = 'Can not be created empty CustomerIdentifier';
			throw new \SS6\ShopBundle\Model\Customer\Exception\EmptyCustomerIdentifierException($message);
		}

		$this->user = $user;
		if ($this->user === null) {
			$this->cartIdentifier = $cartIdentifier;
		}
	}

	/**
	 * @return string
	 */
	public function getCartIdentifier() {
		return $this->cartIdentifier;
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
		return 'session:' . $this->cartIdentifier . ';userId:' . $userId . ';';
	}

}
