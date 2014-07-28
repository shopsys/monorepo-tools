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
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData $customerFormData
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function create(
		CustomerFormData $customerFormData,
		BillingAddress $billingAddress,
		DeliveryAddress $deliveryAddress = null,
		User $userByEmail = null
	) {
		if ($userByEmail instanceof User) {
			if ($userByEmail->getEmail() === $customerFormData->getEmail()) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($customerFormData->getEmail());
			}
		}

		$user = new User(
			$customerFormData->getFirstName(),
			$customerFormData->getLastName(),
			$customerFormData->getEmail(),
			$billingAddress,
			$deliveryAddress
		);
		$this->changePassword($user, $customerFormData->getPassword());

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\CustomerFormData
	 */
	public function edit(User $user, CustomerFormData $customerFormData) {
		$user->edit($customerFormData);

		if ($customerFormData->getPassword() !== null) {
			$this->changePassword($user, $customerFormData->getPassword());
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
