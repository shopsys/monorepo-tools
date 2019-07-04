<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Shopsys\FrameworkBundle\Component\Grid\Grid;
use Shopsys\FrameworkBundle\Model\Security\Roles;
use Shopsys\FrameworkBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\FrameworkBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="administrators",
 *   indexes={
 *     @ORM\Index(columns={"username"})
 *   }
 * )
 */
class Administrator implements UserInterface, Serializable, UniqueLoginInterface, TimelimitLoginInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, unique = true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $realName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=32)
     */
    protected $loginToken;

    /**
     * @var \DateTime
     */
    protected $lastActivity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimit[]|\Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimit",
     *     mappedBy="administrator",
     *     cascade={"persist"},
     *     orphanRemoval=true
     * )
     */
    protected $gridLimits;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    protected $superadmin;

    /**
     * @var bool
     */
    protected $multidomainLogin;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @var string
     */
    protected $multidomainLoginToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $multidomainLoginTokenExpiration;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $administratorData
     */
    public function __construct(AdministratorData $administratorData)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorData $administratorData
     */
    public function edit(AdministratorData $administratorData): void
    {
        $this->email = $administratorData->email;
        $this->realName = $administratorData->realName;
        $this->username = $administratorData->username;
    }

    /**
     * @param string $gridId
     * @return \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimit|null
     */
    public function getGridLimit(string $gridId): ?AdministratorGridLimit
    {
        foreach ($this->gridLimits as $gridLimit) {
            if ($gridLimit->getGridId() === $gridId) {
                return $gridLimit;
            }
        }
        return null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getLoginToken()
    {
        return $this->loginToken;
    }

    /**
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->superadmin;
    }

    /**
     * @param bool $superadmin
     */
    public function setSuperadmin($superadmin)
    {
        $this->superadmin = $superadmin;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param string $realName
     */
    public function setRealname($realName)
    {
        $this->realName = $realName;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash)
    {
        $this->password = $passwordHash;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $loginToken
     */
    public function setLoginToken($loginToken)
    {
        $this->loginToken = $loginToken;
    }

    /**
     * @param \DateTime $lastActivity
     */
    public function setLastActivity($lastActivity)
    {
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
    public function serialize()
    {
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
    public function unserialize($serialized)
    {
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
    public function eraseCredentials()
    {
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        if ($this->superadmin) {
            return [Roles::ROLE_SUPER_ADMIN];
        }
        return [Roles::ROLE_ADMIN];
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null; // bcrypt include salt in password hash
    }

    /**
     * @inheritDoc
     */
    public function isMultidomainLogin()
    {
        return $this->multidomainLogin;
    }

    /**
     * @inheritDoc
     */
    public function setMultidomainLogin($multidomainLogin)
    {
        $this->multidomainLogin = $multidomainLogin;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Grid\Grid $grid
     */
    public function restoreGridLimit(Grid $grid)
    {
        $gridLimit = $this->getGridLimit($grid->getId());
        if ($gridLimit !== null) {
            $grid->setDefaultLimit($gridLimit->getLimit());
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Administrator\AdministratorGridLimit $administratorGridLimit
     */
    public function addGridLimit(AdministratorGridLimit $administratorGridLimit): void
    {
        $this->gridLimits->add($administratorGridLimit);
    }
}
