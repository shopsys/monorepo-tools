<?php

namespace SS6\ShopBundle\Model\Customer;

use DateTime;
use SS6\ShopBundle\Component\String\HashGenerator;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class CustomerPasswordService {

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
