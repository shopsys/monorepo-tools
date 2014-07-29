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
			$userFormData,
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
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressFormData
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function createDeliveryAddress(DeliveryAddressFormData $deliveryAddressFormData) {

		if ($deliveryAddressFormData->getAddressFilled()) {
			$deliveryAddress = new DeliveryAddress($deliveryAddressFormData);
		} else {
			$deliveryAddress = null;
		}

		return $deliveryAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressFormData $deliveryAddressFormData
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function editDeliveryAddress(User $user, DeliveryAddressFormData $deliveryAddressFormData,
		DeliveryAddress $deliveryAddress = null) {

		if ($deliveryAddressFormData->getAddressFilled()) {
			if ($deliveryAddress instanceof DeliveryAddress) {
				$deliveryAddress->edit($deliveryAddressFormData);
			} else {
				$deliveryAddress = new DeliveryAddress($deliveryAddressFormData);
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
