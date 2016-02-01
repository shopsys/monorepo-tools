<?php

namespace SS6\ShopBundle\Model\Customer;

use DateTime;
use SS6\ShopBundle\Component\String\HashGenerator;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class RegistrationService {

	const RESET_PASSWORD_HASH_LENGTH = 50;

	/**
	 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

	/**
	 * @var \SS6\ShopBundle\Component\String\HashGenerator
	 */
	private $hashGenerator;

	public function __construct(
		EncoderFactory $encoderFactory,
		HashGenerator $hashGenerator
	) {
		$this->encoderFactory = $encoderFactory;
		$this->hashGenerator = $hashGenerator;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	public function create(
		UserData $userData,
		BillingAddress $billingAddress,
		DeliveryAddress $deliveryAddress = null,
		User $userByEmail = null
	) {
		if ($userByEmail instanceof User) {
			$isSameEmail = (mb_strtolower($userByEmail->getEmail()) === mb_strtolower($userData->email));
			$isSameDomain = ($userByEmail->getDomainId() === $userData->domainId);
			if ($isSameEmail && $isSameDomain) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($userData->email);
			}
		}

		$user = new User(
			$userData,
			$billingAddress,
			$deliveryAddress
		);
		$this->changePassword($user, $userData->password);

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\UserData
	 */
	public function edit(User $user, UserData $userData) {
		$user->edit($userData);

		if ($userData->password !== null) {
			$this->changePassword($user, $userData->password);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddress|null
	 */
	public function createDeliveryAddress(DeliveryAddressData $deliveryAddressData) {

		if ($deliveryAddressData->addressFilled) {
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

		if ($deliveryAddressData->addressFilled) {
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

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function resetPassword(User $user) {
		$hash = $this->hashGenerator->generateHash(self::RESET_PASSWORD_HASH_LENGTH);
		$user->setResetPasswordHash($hash);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string|null $hash
	 * @return bool
	 */
	public function isResetPasswordHashValid(User $user, $hash) {
		if ($hash === null || $user->getResetPasswordHash() !== $hash) {
			return false;
		}

		$now = new DateTime();
		if ($user->getResetPasswordHashValidThrough() === null || $user->getResetPasswordHashValidThrough() < $now) {
			return false;
		}

		return true;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string|null $hash
	 * @param string $newPassword
	 */
	public function setNewPassword(User $user, $hash, $newPassword) {
		if (!$this->isResetPasswordHashValid($user, $hash)) {
			throw new \SS6\ShopBundle\Model\Customer\Exception\InvalidResetPasswordHashException();
		}

		$this->changePassword($user, $newPassword);
	}

}
