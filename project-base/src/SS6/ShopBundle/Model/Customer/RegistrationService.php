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
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function create(UserData $userData,
			BillingAddress $billingAddress, DeliveryAddress $deliveryAddress = null,
			User $userByEmail = null) {
		if ($userByEmail instanceof User) {
			if (mb_strtolower($userByEmail->getEmail()) === mb_strtolower($userData->getEmail())) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($userData->getEmail());
			}
		}

		$user = new User(
			$userData,
			$billingAddress,
			$deliveryAddress
		);
		$this->changePassword($user, $userData->getPassword());

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\UserData
	 */
	public function edit(User $user, UserData $userData) {
		$user->edit($userData);

		if ($userData->getPassword() !== null) {
			$this->changePassword($user, $userData->getPassword());
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function createDeliveryAddress(DeliveryAddressData $deliveryAddressData) {

		if ($deliveryAddressData->getAddressFilled()) {
			$deliveryAddress = new DeliveryAddress($deliveryAddressData);
		} else {
			$deliveryAddress = null;
		}

		return $deliveryAddress;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressData $deliveryAddressData
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function editDeliveryAddress(User $user, DeliveryAddressData $deliveryAddressData,
		DeliveryAddress $deliveryAddress = null) {

		if ($deliveryAddressData->getAddressFilled()) {
			if ($deliveryAddress instanceof DeliveryAddress) {
				$deliveryAddress->edit($deliveryAddressData);
			} else {
				$deliveryAddress = new DeliveryAddress($deliveryAddressData);
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
			$email = mb_strtolower($email);
		}

		if ($userByEmail instanceof User) {
			if (mb_strtolower($userByEmail->getEmail()) === $email && $user !== $userByEmail) {
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
