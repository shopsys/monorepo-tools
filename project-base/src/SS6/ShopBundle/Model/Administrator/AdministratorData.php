<?php

namespace SS6\ShopBundle\Model\Administrator;

class AdministratorData {

	/**
	 * @var string|null
	 */
	private $username;

	/**
	 * @var string|null
	 */
	private $realName;

	/**
	 * @var string|null
	 */
	private $password;

	/**
	 * @var string|null
	 */
	private $email;

	/**
	 * @param string|null $userName
	 * @param string|null $realName
	 * @param string|null $password
	 * @param string|null $email
	 */
	public function __construct($userName = null, $realName = null, $password = null, $email = null) {
		$this->username = $userName;
		$this->realName = $realName;
		$this->password = $password;
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getRealName() {
		return $this->realName;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param string $realName
	 */
	public function setRealName($realName) {
		$this->realName = $realName;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 */
	public function setFromEntity(Administrator $administrator) {
		$this->setEmail($administrator->getEmail());
		$this->setRealName($administrator->getRealName());
		$this->setUsername($administrator->getUsername());
	}


}
