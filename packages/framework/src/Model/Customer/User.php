<?php

namespace Shopsys\FrameworkBundle\Model\Customer;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Shopsys\FrameworkBundle\Component\String\HashGenerator;
use Shopsys\FrameworkBundle\Model\Security\Roles;
use Shopsys\FrameworkBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(
 *     name="users",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="email_domain", columns={"email", "domain_id"})
 *     },
 *     indexes={
 *         @ORM\Index(columns={"email"})
 *     }
 * )
 * @ORM\Entity
 */
class User implements UserInterface, TimelimitLoginInterface, Serializable
{
    const RESET_PASSWORD_HASH_LENGTH = 50;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $password;

    /**
     * @var \DateTime
     */
    protected $lastActivity;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     * @ORM\OneToOne(targetEntity="Shopsys\FrameworkBundle\Model\Customer\BillingAddress", cascade={"persist"})
     * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id", nullable=false)
     */
    protected $billingAddress;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     * @ORM\OneToOne(targetEntity="Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress", cascade={"persist"}, orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    protected $deliveryAddress;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $domainId;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup")
     * @ORM\JoinColumn(name="pricing_group_id", referencedColumnName="id", nullable=false)
     */
    protected $pricingGroup;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $resetPasswordHash;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $resetPasswordHashValidThrough;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected $telephone;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     * @param \Shopsys\FrameworkBundle\Model\Customer\BillingAddress $billingAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $userByEmail
     */
    public function __construct(
        UserData $userData,
        BillingAddress $billingAddress,
        ?DeliveryAddress $deliveryAddress,
        ?self $userByEmail
    ) {
        $this->firstName = $userData->firstName;
        $this->lastName = $userData->lastName;
        $this->billingAddress = $billingAddress;
        $this->deliveryAddress = $deliveryAddress;
        if ($userData->createdAt !== null) {
            $this->createdAt = $userData->createdAt;
        } else {
            $this->createdAt = new \DateTime();
        }
        $this->domainId = $userData->domainId;
        $this->pricingGroup = $userData->pricingGroup;
        $this->telephone = $userData->telephone;

        $this->changeEmail($userData->email, $userByEmail);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\UserData $userData
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     */
    public function edit(UserData $userData, EncoderFactoryInterface $encoderFactory)
    {
        $this->firstName = $userData->firstName;
        $this->lastName = $userData->lastName;
        $this->pricingGroup = $userData->pricingGroup;
        $this->telephone = $userData->telephone;

        if ($userData->password !== null) {
            $this->changePassword($encoderFactory, $userData->password);
        }
    }

    /**
     * @param string $email
     * @param \Shopsys\FrameworkBundle\Model\Customer\User|null $userByEmail
     */
    public function changeEmail(string $email, ?self $userByEmail)
    {
        $email = mb_strtolower($email);

        if ($this !== $userByEmail) {
            $this->checkDuplicateEmail($email, $this->domainId, $userByEmail);
        }

        $this->email = $email;
    }

    /**
     * @param string $email
     * @param int $domainId
     * @param self|null $userByEmail
     */
    protected function checkDuplicateEmail(string $email, int $domainId, ?self $userByEmail): void
    {
        if ($userByEmail === null) {
            return;
        }

        $isSameEmail = ($userByEmail->getEmail() === $email);
        $isSameDomain = ($userByEmail->getDomainId() === $domainId);
        if ($isSameEmail && $isSameDomain) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException($email);
        }
    }

    /**
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param string $password
     */
    public function changePassword(EncoderFactoryInterface $encoderFactory, $password)
    {
        $encoder = $encoderFactory->getEncoder($this);
        $passwordHash = $encoder->encodePassword($password, null);
        $this->password = $passwordHash;
        $this->resetPasswordHash = null;
        $this->resetPasswordHashValidThrough = null;
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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @param \DateTime $lastActivity
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    public function onLogin()
    {
        $this->lastLogin = new DateTime();
    }

    /**
     * @return int
     */
    public function getDomainId()
    {
        return $this->domainId;
    }

    /**
     * @param int $domainId
     */
    public function setDomainId($domainId)
    {
        $this->domainId = $domainId;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
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
    public function getUsername()
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
    public function getFullName()
    {
        if ($this->billingAddress->isCompanyCustomer()) {
            return $this->billingAddress->getCompanyName();
        }

        return $this->lastName . ' ' . $this->firstName;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress|null
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function getPricingGroup()
    {
        return $this->pricingGroup;
    }

    /**
     * @return string|null
     */
    public function getResetPasswordHash()
    {
        return $this->resetPasswordHash;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            // When unserialized user has empty password,
            // then UsernamePasswordToken is reloaded and ROLE_ADMIN_AS_CUSTOMER is lost.
            $this->password,
            time(), // lastActivity
            $this->domainId,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $timestamp,
            $this->domainId
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
        return [Roles::ROLE_LOGGED_CUSTOMER];
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null; // bcrypt include salt in password hash
    }

    /**
     * @return string|null
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
     * @param \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressFactoryInterface $deliveryAddressFactory
     */
    public function editDeliveryAddress(
        DeliveryAddressData $deliveryAddressData,
        DeliveryAddressFactoryInterface $deliveryAddressFactory
    ) {
        if (!$deliveryAddressData->addressFilled) {
            $this->deliveryAddress = null;
            return;
        }

        if ($this->deliveryAddress instanceof DeliveryAddress) {
            $this->deliveryAddress->edit($deliveryAddressData);
        } else {
            $this->deliveryAddress = $deliveryAddressFactory->create($deliveryAddressData);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\String\HashGenerator $hashGenerator
     */
    public function resetPassword(HashGenerator $hashGenerator): void
    {
        $hash = $hashGenerator->generateHash(self::RESET_PASSWORD_HASH_LENGTH);
        $this->resetPasswordHash = $hash;
        $this->resetPasswordHashValidThrough = new DateTime('+48 hours');
    }

    /**
     * @param string|null $hash
     * @return bool
     */
    public function isResetPasswordHashValid(?string $hash): bool
    {
        if ($hash === null || $this->resetPasswordHash !== $hash) {
            return false;
        }

        $now = new DateTime();

        return $this->resetPasswordHashValidThrough !== null && $this->resetPasswordHashValidThrough >= $now;
    }

    /**
     * @param \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $encoderFactory
     * @param string|null $hash
     * @param string $newPassword
     */
    public function setNewPassword(EncoderFactoryInterface $encoderFactory, ?string $hash, string $newPassword)
    {
        if (!$this->isResetPasswordHashValid($hash)) {
            throw new \Shopsys\FrameworkBundle\Model\Customer\Exception\InvalidResetPasswordHashException();
        }

        $this->changePassword($encoderFactory, $newPassword);
    }
}
