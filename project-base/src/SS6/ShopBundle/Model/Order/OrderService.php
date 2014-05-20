<?php

namespace SS6\ShopBundle\Model\Order;

class OrderService {

	/**
	 * @param array \SS6\ShopBundle\Model\Order\Order[]
	 */
	public function detachCustomer(array $orders) {
		foreach ($orders as $order) {
			/* @var $order \SS6\ShopBundle\Model\Order\Order */
			$order->detachCustomer();
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $email
	 * @param string|null $password
	 * @param \SS6\ShopBundle\Model\Customer\User|null $userByEmail
	 * @throws \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException
	 */
	public function edit(User $user, $firstName, $lastName, $email, $password = null,
			User $userByEmail = null) {
		if ($userByEmail instanceof User) {
			if ($userByEmail->getEmail() === $email && $user !== $userByEmail) {
				throw new \SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException($email);
			}
		}

		$user->edit($firstName, $lastName, $email);

		if ($password !== null) {
			$this->changePassword($user, $password);
		}
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
