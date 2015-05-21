<?php

namespace SS6\ShopBundle\Model\Administrator;

class AdministratorData {

	/**
	 * @var bool
	 */
	public $superadmin;

	/**
	 * @var string|null
	 */
	public $username;

	/**
	 * @var string|null
	 */
	public $realName;

	/**
	 * @var string|null
	 */
	public $password;

	/**
	 * @var string|null
	 */
	public $email;

	/**
	 * @param bool $superadmin
	 * @param string|null $username
	 * @param string|null $realName
	 * @param string|null $password
	 * @param string|null $email
	 */
	public function __construct($superadmin = false, $username = null, $realName = null, $password = null, $email = null) {
		$this->username = $username;
		$this->realName = $realName;
		$this->password = $password;
		$this->email = $email;
		$this->superadmin = $superadmin;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 */
	public function setFromEntity(Administrator $administrator) {
		$this->email = $administrator->getEmail();
		$this->realName = $administrator->getRealName();
		$this->username = $administrator->getUsername();
		$this->superadmin = $administrator->isSuperadmin();
	}

}
