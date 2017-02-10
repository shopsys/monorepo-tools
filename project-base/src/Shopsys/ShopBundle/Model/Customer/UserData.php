<?php

namespace Shopsys\ShopBundle\Model\Customer;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;

class UserData {

	/**
	 * @var string|null
	 */
	public $firstName;

	/**
	 * @var string|null
	 */
	public $lastName;

	/**
	 * @var string|null
	 */
	public $email;

	/**
	 * @var string|null
	 */
	public $password;

	/**
	 * @var int
	 */
	public $domainId;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup
	 */
	public $pricingGroup;

	/**
	 * @param int $domainId
	 * @param string|null $firstName
	 * @param string|null $lastName
	 * @param string|null $email
	 * @param string|null $password
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup|null $pricingGroup
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
	 * @param \Shopsys\ShopBundle\Model\Customer\User $user
	 */
	public function setFromEntity(User $user) {
		$this->domainId = $user->getDomainId();
		$this->firstName = $user->getFirstName();
		$this->lastName = $user->getLastName();
		$this->email = $user->getEmail();
		$this->pricingGroup = $user->getPricingGroup();
	}

}
