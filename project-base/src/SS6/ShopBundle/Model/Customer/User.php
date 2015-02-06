<?php

namespace SS6\ShopBundle\Model\Customer;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Security\Roles;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *   name="users",
 *   uniqueConstraints={
 *		@ORM\UniqueConstraint(name="email_domain", columns={"email", "domain_id"})
 *   },
 *   indexes={
 *     @ORM\Index(columns={"email"})
 *   }
 * )
 * @ORM\Entity
 */
class User implements UserInterface, TimelimitLoginInterface, Serializable {

	/**
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $lastName;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\Email(message = "E-mail '{{ value }}' není validní.")
	 */
	private $email;

	/**
	 * @ORM\Column(name="password", type="string", length=100)
	 */
	private $password;

	/**
	 * @var DateTime
	 */
	private $lastActivity;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\BillingAddress
	 * @ORM\OneToOne(targetEntity="SS6\ShopBundle\Model\Customer\BillingAddress")
	 * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id", nullable=false)
	 */
	private $billingAddress;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 * @ORM\OneToOne(targetEntity="SS6\ShopBundle\Model\Customer\DeliveryAddress")
	 */
	private $deliveryAddress;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $lastLogin;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $domainId;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 * @ORM\ManyToOne(targetEntity="SS6\ShopBundle\Model\Pricing\Group\PricingGroup")
	 * @ORM\JoinColumn(name="pricing_group_id", referencedColumnName="id", nullable=false)
	 */
	private $pricingGroup;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $resetPasswordHash;

	/**
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $resetPasswordHashValidThrough;

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 */
	public function __construct(
		UserData $userData,
		BillingAddress $billingAddress,
		DeliveryAddress $deliveryAddress = null
	) {
		$this->firstName = $userData->firstName;
		$this->lastName = $userData->lastName;
		$this->email = mb_strtolower($userData->email);
		$this->billingAddress = $billingAddress;
		$this->deliveryAddress = $deliveryAddress;
		$this->createdAt = new DateTime();
		$this->domainId = $userData->domainId;
		$this->pricingGroup = $userData->pricingGroup;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 */
	public function edit(UserData $userData) {
		$this->firstName = $userData->firstName;
		$this->lastName = $userData->lastName;
		$this->pricingGroup = $userData->pricingGroup;
	}

	/**
	 * @param string $email
	 */
	public function changeEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param string $password
	 */
	public function changePassword($password) {
		$this->password = $password;
	}

	/**
	 * @param string $hash
	 */
	public function setResetPasswordHash($hash) {
		$validThrough = new DateTime();
		$validThrough->modify('+48 hours');
		$this->resetPasswordHash = $hash;
		$this->resetPasswordHashValidThrough = $validThrough;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 */
	public function setDeliveryAddress(DeliveryAddress $deliveryAddress = null) {
		$this->deliveryAddress = $deliveryAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	public function setPricingGroup(PricingGroup $pricingGroup) {
		$this->pricingGroup = $pricingGroup;
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
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @return DateTime
	 */
	public function getLastActivity() {
		return $this->lastActivity;
	}

	/**
	 * @param DateTime $lastActivity
	 */
	public function setLastActivity($lastActivity) {
		$this->lastActivity = $lastActivity;
	}

	public function onLogin() {
		$this->lastLogin = new DateTime();
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @param int $domainId
	 */
	public function setDomainId($domainId) {
		$this->domainId = $domainId;
	}

	/**
	 * @return string
	 */
	public function getLastName() {
		return $this->lastName;
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
	public function getUsername() {
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
	public function getFullName() {
		return $this->firstName . ' ' . $this->lastName;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\BillingAddress
	 */
	public function getBillingAddress() {
		return $this->billingAddress;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function getDeliveryAddress() {
		return $this->deliveryAddress;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}

	/**
	 * @return \DateTime|null
	 */
	public function getLastLogin() {
		return $this->lastLogin;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getPricingGroup() {
		return $this->pricingGroup;
	}

	/**
	 * @return string
	 */
	public function getResetPasswordHash() {
		return $this->resetPasswordHash;
	}

	/**
	 * @inheritDoc
	 */
	public function serialize() {
		return serialize([
			$this->id,
			$this->email,
			$this->password,
			time(), // lastActivity
			$this->domainId,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize($serialized) {
		list(
			$this->id,
			$this->email,
			$this->password,
			$timestamp,
			$this->domainId,
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
		return [Roles::ROLE_CUSTOMER];
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		return null; // bcrypt include salt in password hash
	}

}
