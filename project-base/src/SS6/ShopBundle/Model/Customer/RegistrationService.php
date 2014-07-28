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
	 * @param \SS6\ShopBundle\Model\Customer\UserFormData $userFormData
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function create(UserFormData $userFormData,
			BillingAddress $billingAddress, DeliveryAddress $deliveryAddress = null,
			User $userByEmail = null) {
		if ($userByEmail instanceof User) {
			if ($userByEmail->getEmail() === $userFormData->getEmail()) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($userFormData->getEmail());
			}
		}

		$user = new User(
			$userFormData->getFirstName(),
			$userFormData->getLastName(),
			$userFormData->getEmail(),
			$billingAddress,
			$deliveryAddress
		);
		$this->changePassword($user, $userFormData->getPassword());

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\UserFormData
	 */
	public function edit(User $user, UserFormData $userFormData) {
		$user->edit($userFormData);

		if ($userFormData->getPassword() !== null) {
			$this->changePassword($user, $userFormData->getPassword());
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData $customerFormData
	 * @return \SS6\ShopBundle\Model\Customer\BillingAddress
	 */
	public function editBillingAddress(BillingAddress $billingAddress, CustomerFormData $customerFormData) {
		$billingAddress->edit($customerFormData);

		return $billingAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function createDeliveryAddress(CustomerFormData $customerFormData) {

		if ($customerFormData->getDeliveryAddressFilled()) {
			$deliveryAddress = new DeliveryAddress(
				$customerFormData->getDeliveryStreet(),
				$customerFormData->getDeliveryCity(),
				$customerFormData->getDeliveryPostcode(),
				$customerFormData->getDeliveryCountry(),
				$customerFormData->getDeliveryCompanyName(),
				$customerFormData->getDeliveryContactPerson(),
				$customerFormData->getDeliveryTelephone()
			);
		} else {
			$deliveryAddress = null;
		}

		return $deliveryAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData $customerFormData
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function editDeliveryAddress(User $user, CustomerFormData $customerFormData,
		DeliveryAddress $deliveryAddress = null) {

		if ($customerFormData->getDeliveryAddressFilled()) {
			if ($deliveryAddress instanceof DeliveryAddress) {
				$deliveryAddress->edit($customerFormData);
			} else {
				$deliveryAddress = new DeliveryAddress(
					$customerFormData->getDeliveryStreet(),
					$customerFormData->getDeliveryCity(),
					$customerFormData->getDeliveryPostcode(),
					$customerFormData->getDeliveryCountry(),
					$customerFormData->getDeliveryCompanyName(),
					$customerFormData->getDeliveryContactPerson(),
					$customerFormData->getDeliveryTelephone()
				);
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
