<?php

namespace SS6\ShopBundle\Model\Administrator;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use SS6\ShopBundle\Model\Administrator\AdministratorGridLimit;
use SS6\ShopBundle\Model\Security\Roles;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="administrators",
 *   indexes={
 *     @ORM\Index(columns={"username"})
 *   }
 * )
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Administrator implements UserInterface, Serializable, UniqueLoginInterface, TimelimitLoginInterface {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=100, unique = true)
	 */
	private $username;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $realName;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=32)
	 */
	private $loginToken;

	/**
	 * @var \DateTime
	 */
	private $lastActivity;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $email;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorGridLimit[]
	 * @ORM\OneToMany(
	 *	targetEntity="SS6\ShopBundle\Model\Administrator\AdministratorGridLimit",
	 *	mappedBy="administrator",
	 *	orphanRemoval=true
	 * )
	 */
	private $gridLimits;

	/**
	 * @ORM\Column(type="boolean")
	 *
	 * @var bool
	 */
	private $superadmin;

	/**
	 * @var bool
	 */
	private $multidomainLogin;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 *
	 * @var string
	 */
	private $multidomainLoginToken;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $multidomainLoginTokenExpiration;

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorData $administratorData
	 */
	public function __construct(AdministratorData $administratorData) {
		$this->email = $administratorData->email;
		$this->realName = $administratorData->realName;
		$this->username = $administratorData->username;
		$this->lastActivity = new DateTime();
		$this->gridLimits = new ArrayCollection();
		$this->loginToken = '';
		$this->superadmin = $administratorData->superadmin;
		$this->multidomainLogin = false;
		$this->multidomainLoginToken = '';
		$this->multidomainLoginTokenExpiration = new DateTime();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorData $administratorData
	 */
	public function edit(AdministratorData $administratorData) {
		$this->email = $administratorData->email;
		$this->realName = $administratorData->realName;
		$this->username = $administratorData->username;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorGridLimit
	 */
	public function addGridLimit(AdministratorGridLimit $gridLimit) {
		if (!$this->gridLimits->contains($gridLimit)) {
			$this->gridLimits->add($gridLimit);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\AdministratorGridLimit $gridLimit
	 */
	public function removeGridLimit(AdministratorGridLimit $gridLimit) {
		$this->gridLimits->removeElement($gridLimit);
	}

	/**
	 * @param string $gridId
	 * @return \SS6\ShopBundle\Model\Administrator\AdministratorGridLimit
	 */
	public function getGridLimit($gridId) {
		foreach ($this->gridLimits as $gridLimit) {
			if ($gridLimit->getGridId() === $gridId) {
				return $gridLimit;
			}
		}
		return null;
	}

	/**
	 * @param string $gridId
	 * @return int|null
	 */
	public function getLimitByGridId($gridId) {
		$gridLimit = $this->getGridLimit($gridId);
		if ($gridLimit !== null) {
			return $gridLimit->getLimit();
		}
		return null;
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
	public function getEmail() {
		return $this->email;
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
	 * @return bool
	 */
	public function isSuperadmin() {
		return $this->superadmin;
	}

	/**
	 * @param bool $superadmin
	 */
	public function setSuperadmin($superadmin) {
		$this->superadmin = $superadmin;
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
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param string $loginToken
	 */
	public function setLoginToken($loginToken) {
		$this->loginToken = $loginToken;
	}

	/**
	 * @param \DateTime $lastActivity
	 */
	public function setLastActivity($lastActivity) {
		$this->lastActivity = $lastActivity;
	}

	/**
	 * @param string $multidomainLoginToken
	 * @param \DateTime $multidomainLoginTokenExpiration
	 */
	public function setMultidomainLoginTokenWithExpiration(
		$multidomainLoginToken,
		DateTime $multidomainLoginTokenExpiration
	) {
		$this->multidomainLoginToken = $multidomainLoginToken;
		$this->multidomainLoginTokenExpiration = $multidomainLoginTokenExpiration;
	}

	/**
	 * @inheritDoc
	 */
	public function serialize() {
		return serialize([
			$this->id,
			$this->username,
			$this->password,
			$this->realName,
			$this->loginToken,
			time(),
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize($serialized) {
		list(
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
		if ($this->superadmin) {
			return [Roles::ROLE_SUPER_ADMIN];
		}
		return [Roles::ROLE_ADMIN];
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		return null; // bcrypt include salt in password hash
	}

	/**
	 * @inheritDoc
	 */
	public function isMultidomainLogin() {
		return $this->multidomainLogin;
	}

	/**
	 * @inheritDoc
	 */
	public function setMultidomainLogin($multidomainLogin) {
		$this->multidomainLogin = $multidomainLogin;
	}

}
