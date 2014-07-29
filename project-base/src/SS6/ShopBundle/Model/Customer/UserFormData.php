<?php

namespace SS6\ShopBundle\Model\Customer;

class UserFormData {

	/**
	 * @var string
	 */
	private $firstName;

	/**
	 * @var string
	 */
	private $lastName;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $password
	 */
	public function __construct($firstName = null, $lastName = null, $email = null, $password = null) {
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->password = $password;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function getLastName() {
		return $this->lastName;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function setEmail($email) {
		$this->email = $email;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function setFromEntity(User $user) {
		$this->firstName = $user->getFirstName();
		$this->lastName = $user->getLastName();
		$this->email = $user->getEmail();
	}

}
