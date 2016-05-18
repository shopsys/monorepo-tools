<?php

namespace SS6\ShopBundle\Model\Customer;

class RegistrationService {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerPasswordService
	 */
	private $customerPasswordService;

	public function __construct(CustomerPasswordService $customerPasswordService) {
		$this->customerPasswordService = $customerPasswordService;
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
		$this->customerPasswordService->changePassword($user, $userData->password);

		return $user;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Customer\UserData
	 */
	public function edit(User $user, UserData $userData) {
		$user->edit($userData);

		if ($userData->password !== null) {
			$this->customerPasswordService->changePassword($user, $userData->password);
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

}
