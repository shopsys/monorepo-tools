<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;

class UserData {

	/**
	 * @var string|null
	 */
	private $firstName;

	/**
	 * @var string|null
	 */
	private $lastName;

	/**
	 * @var string|null
	 */
	private $email;

	/**
	 * @var string|null
	 */
	private $password;

	/**
	 * @var int
	 */
	private $domainId;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	private $pricingGroup;

	/**
	 * @param int $domainId
	 * @param string|null $firstName
	 * @param string|null $lastName
	 * @param string|null $email
	 * @param string|null $password
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup|null $pricingGroup
	 */
	public function __construct(
		$domainId = 1,
		$firstName = null,
		$lastName = null,
		$email = null,
		$password = null,
		PricingGroup $pricingGroup = null
	) {
		$this->domainId = $domainId;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->password = $password;
		$this->pricingGroup = $pricingGroup;
	}

	/**
	 * @return string|null
	 */
	public function getFirstName() {
		return $this->firstName;
	}

	/**
	 * @return string|null
	 */
	public function getLastName() {
		return $this->lastName;
	}

	/**
	 * @return string|null
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @return string|null
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public function getPricingGroup() {
		return $this->pricingGroup;
	}

	/**
	 * @param int $domainId
	 */
	public function setDomainId($domainId) {
		$this->domainId = $domainId;
	}

	/**
	 * @param string|null $firstName
	 */
	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	/**
	 * @param string|null $lastName
	 */
	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	/**
	 * @param string|null $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @param string|null $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	public function setPricingGroup(PricingGroup $pricingGroup) {
		$this->pricingGroup = $pricingGroup;
	}
	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function setFromEntity(User $user) {
		$this->domainId = $user->getDomainId();
		$this->firstName = $user->getFirstName();
		$this->lastName = $user->getLastName();
		$this->email = $user->getEmail();
		$this->pricingGroup = $user->getPricingGroup();
	}

}
