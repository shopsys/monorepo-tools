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
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress $deliveryAddress
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @return \SS6\ShopBundle\Model\Customer\User
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function create($firstName, $lastName, $email, $password,
			BillingAddress $billingAddress, DeliveryAddress $deliveryAddress,
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
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string $email
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function changeEmail(User $user, $email, User $userByEmail = null) {
		if ($userByEmail instanceof User) {
			if ($userByEmail->getEmail() === $email && $user !== $userByEmail) {
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
