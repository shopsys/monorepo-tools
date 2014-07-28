<?php

namespace SS6\ShopBundle\Model\Customer;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use SS6\ShopBundle\Model\Security\Roles;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *   name="users",
 *   indexes={
 *     @ORM\Index(columns={"email"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="SS6\ShopBundle\Model\Customer\SecurityUserRepository")
 */
class User implements UserInterface, TimelimitLoginInterface, Serializable {

	/**
	 * @ORM\Column(name="id", type="integer")
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
	 * @ORM\Column(type="string", length=255, unique=true)
	 * @Assert\Email(message = "E-mail '{{ value }}' není validní.")
	 */
	protected $email;

	/**
	 * @ORM\Column(name="password", type="string", length=100)
	 */
	protected $password;
	
	/**
	 * @var DateTime 
	 */
	protected $lastActivity;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\BillingAddress
	 * @ORM\OneToOne(targetEntity="SS6\ShopBundle\Model\Customer\BillingAddress")
	 * @ORM\JoinColumn(name="billing_address_id", referencedColumnName="id", nullable=false)
	 */
	protected $billingAddress;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 * @ORM\OneToOne(targetEntity="SS6\ShopBundle\Model\Customer\DeliveryAddress")
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
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 */
	public function __construct($firstName, $lastName, $email,
			BillingAddress $billingAddress, DeliveryAddress $deliveryAddress = null) {
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = strtolower($email);
		$this->billingAddress = $billingAddress;
		$this->deliveryAddress = $deliveryAddress;
		$this->createdAt = new DateTime();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData $customerFormData
	 */
	public function edit($customerFormData) {
		$this->firstName = $customerFormData->getFirstName();
		$this->lastName = $customerFormData->getLastName();
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
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 */
	public function setDeliveryAddress(DeliveryAddress $deliveryAddress = null) {
		$this->deliveryAddress = $deliveryAddress;
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
	 * @inheritDoc
	 */
	public function serialize() {
		return serialize(array(
			$this->id,
			$this->email,
			$this->password,
			time(), // lastActivity
		));
	}

	/**
	 * @inheritDoc
	 */
	public function unserialize($serialized) {
		list (
			$this->id,
			$this->email,
			$this->password,
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
		return array(Roles::ROLE_CUSTOMER);
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt() {
		return null; // bcrypt include salt in password hash
	}

}
