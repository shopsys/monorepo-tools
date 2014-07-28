<?php

namespace SS6\ShopBundle\Model\Customer;

class UserFormData {

	/**
	 * @var int
	 */
	private $id;

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

	public function getId() {
		return $this->id;
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

	public function setId($id) {
		$this->id = $id;
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
		$this->id = $user->getId();
		$this->firstName = $user->getFirstName();
		$this->lastName = $user->getLastName();
		$this->email = $user->getEmail();
	}

}
