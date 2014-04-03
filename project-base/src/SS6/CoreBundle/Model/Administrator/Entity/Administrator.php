<?php

namespace SS6\CoreBundle\Model\Administrator\Entity;

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="administrators")
 * @ORM\Entity(repositoryClass="SS6\CoreBundle\Model\Administrator\Repository\AdministratorRepository")
 */
class Administrator implements UserInterface, Serializable {

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
	protected $realname;

	/**
	 * @ORM\Column(name="password", type="string", length=100)
	 */
	protected $password;
	
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
	public function getRealname() {
		return $this->realname;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param string $realname
	 */
	public function setRealname($realname) {
		$this->realname = $realname;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @inheritDoc
	 */
	public function serialize() {
		return serialize(array(
			$this->id,
			$this->username,
			$this->password,
			$this->realname,
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
			$this->realname
		) = unserialize($serialized);
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
		return array('ROLE_ADMIN');
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		return null;
	}

}
