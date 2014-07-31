<?php

namespace SS6\ShopBundle\Model\Customer;

class UserData {

	/**
	 * @var string|null
	 */
	private $firstName;

	/**
	 * @var string|null
	 */
	private $lastName;

	/**
	 * @var string|null
	 */
	private $email;

	/**
	 * @var string|null
	 */
	private $password;

	/**
	 * @param string|null $firstName
	 * @param string|null $lastName
	 * @param string|null $email
	 * @param string|null $password
	 */
	public function __construct($firstName = null, $lastName = null, $email = null, $password = null) {
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->password = $password;
	}

	/**
	 * @return string|null
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @return string|null
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @return string|null
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string|null
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string|null $firstName
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * @param string|null $lastName
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * @param string|null $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param string|null $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function setFromEntity(User $user) {
		$this->firstName = $user->getFirstName();
		$this->lastName = $user->getLastName();
		$this->email = $user->getEmail();
	}

}
