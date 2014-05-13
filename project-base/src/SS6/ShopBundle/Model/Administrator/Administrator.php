<?php

namespace SS6\ShopBundle\Model\Administrator;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use SS6\ShopBundle\Model\Security\Roles;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="administrators")
 * @ORM\Entity(repositoryClass="SS6\ShopBundle\Model\Administrator\AdministratorRepository")
 */
class Administrator implements UserInterface, Serializable, UniqueLoginInterface, TimelimitLoginInterface {

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	protected $realName;

	/**
	 * @ORM\Column(name="password", type="string", length=100)
	 */
	protected $password;
	
	/**
	 * @ORM\Column(name="login_token", type="string", length=32)
	 */
	protected $loginToken;
	
	/**
	 * @var DateTime 
	 */
	protected $lastActivity;
	
	public function __construct() {
		$this->lastActivity = new DateTime();
	}
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	public function getLoginToken() {
		return $this->loginToken;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getLastActivity() {
		return $this->lastActivity;
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
	public function setRealname($realName) {
		$this->realName = $realName;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}
	
	/**
	 * @param string $loginToken
	 */
	public function setLoginToken($loginToken) {
		$this->loginToken = $loginToken;
	}
	
	/**
	 * @param DateTime $lastActivity
	 */
	public function setLastActivity($lastActivity) {
		$this->lastActivity = $lastActivity;
	}

	/**
	 * @inheritDoc
	 */
	public function serialize() {
		return serialize(array(
			$this->id,
			$this->username,
			$this->password,
			$this->realName,
			$this->loginToken,
			time(),
		));
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize($serialized) {
		list (
			$this->id,
			$this->username,
			$this->password,
			$this->realName,
			$this->loginToken,
			$timestamp
		) = unserialize($serialized);
		$this->lastActivity = new DateTime();
		$this->lastActivity->setTimestamp($timestamp);
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials() {
		
	}

	/**
	 * @inheritDoc
	 */
	public function getRoles() {
		return array(Roles::ROLE_ADMIN);
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		return null; // bcrypt include salt in password hash
	}

}
