<?php

namespace SS6\ShopBundle\Model\Customer;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class RegistrationService {

	/**
	 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

	/**
	 * @param \Symfony\Component\Security\Core\Encoder\EncoderFactory $encoderFactory
	 */
	public function __construct(EncoderFactory $encoderFactory) {
		$this->encoderFactory = $encoderFactory;
	}

	/**
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string $password
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function create($firstName, $lastName, $email, $password,
			BillingAddress $billingAddress, DeliveryAddress $deliveryAddress = null,
			User $userByEmail = null) {
		if ($userByEmail instanceof User) {
			if ($userByEmail->getEmail() === $email) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($email);
			}
		}

		$user = new User($firstName, $lastName, $email, $billingAddress, $deliveryAddress);
		$this->changePassword($user, $password);

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string $firstName
	 * @param string $lastName
	 * @param string|null $password
	 */
	public function edit(User $user, $firstName, $lastName, $password = null) {
		$user->edit($firstName, $lastName);

		if ($password !== null) {
			$this->changePassword($user, $password);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param boolean $companyCustomer
	 * @param string|null $companyName
	 * @param string|null $companyNumber
	 * @param string|null $companyTaxNumber
	 * @param string|null $telephone
	 * @return \SS6\ShopBundle\Model\Customer\BillingAddress
	 */
	public function editBillingAddress(BillingAddress $billingAddress,
			$street = null, $city = null, $postcode = null, $country = null,
			$companyCustomer = false, $companyName = null, $companyNumber = null, $companyTaxNumber = null,
			$telephone = null) {

		$billingAddress->edit(
			$street,
			$city,
			$postcode,
			$country,
			$companyCustomer,
			$companyName,
			$companyNumber,
			$companyTaxNumber,
			$telephone
		);

		return $billingAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param boolean $addressFilled
	 * @param string|null $companyName
	 * @param string|null $contactPerson
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param string|null $telephone
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function createDeliveryAddress($addressFilled = false, $companyName = null, $contactPerson = null,
			$street = null, $city = null, $postcode = null, $country = null, $telephone = null) {

		if ($addressFilled) {
			$deliveryAddress = new DeliveryAddress($street, $city, $postcode, $country, $companyName, $contactPerson, $telephone);
		} else {
			$deliveryAddress = null;
		}

		return $deliveryAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param boolean $addressFilled
	 * @param string|null $companyName
	 * @param string|null $contactPerson
	 * @param string|null $street
	 * @param string|null $city
	 * @param string|null $postcode
	 * @param string|null $country
	 * @param string|null $telephone
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function editDeliveryAddress(User $user, DeliveryAddress $deliveryAddress = null,
			$addressFilled = false, $companyName = null, $contactPerson = null, $street = null,
			$city = null, $postcode = null, $country = null, $telephone = null) {

		if ($addressFilled) {
			if ($deliveryAddress instanceof DeliveryAddress) {
				$deliveryAddress->edit($street, $city, $postcode, $country, $companyName, $contactPerson, $telephone);
			} else {
				$deliveryAddress = new DeliveryAddress($street, $city, $postcode, $country, $companyName, $contactPerson, $telephone);
				$user->setDeliveryAddress($deliveryAddress);
			}
		} else {
			$user->setDeliveryAddress(null);
			$deliveryAddress = null;
		}

		return $deliveryAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string $email
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function changeEmail(User $user, $email, User $userByEmail = null) {
		if ($email !== null) {
			$email = strtolower($email);
		}

		if ($userByEmail instanceof User) {
			if (strtolower($userByEmail->getEmail()) === $email && $user !== $userByEmail) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($email);
			}
		}

		$user->changeEmail($email);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string $password
	 */
	public function changePassword(User $user, $password) {
		$encoder = $this->encoderFactory->getEncoder($user);
		$passwordHash = $encoder->encodePassword($password, $user->getSalt());
		$user->changePassword($passwordHash);
	}

}
