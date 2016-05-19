<?php

namespace SS6\ShopBundle\Model\Customer;

use SS6\ShopBundle\Component\Condition;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerPasswordService;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Order\Order;

class CustomerService {

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

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @return \SS6\ShopBundle\Model\Customer\CustomerData
	 */
	public function getAmendedCustomerDataByOrder(User $user, Order $order) {
		$billingAddress = $user->getBillingAddress();
		$deliveryAddress = $user->getDeliveryAddress();

		return new CustomerData(
			new UserData(
				1,
				Condition::ifNull($user->getFirstName(), $order->getFirstName()),
				Condition::ifNull($user->getLastName(), $order->getLastName()),
				null,
				null,
				$user->getPricingGroup()
			),
			$this->getAmendedBillingAddressDataByOrder($order, $billingAddress),
			$this->getAmendedDeliveryAddressDataByOrder($order, $deliveryAddress)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @return \SS6\ShopBundle\Model\Customer\BillingAddressData
	 */
	private function getAmendedBillingAddressDataByOrder(Order $order, BillingAddress $billingAddress) {
		$billingAddressData = new BillingAddressData();
		$billingAddressData->setFromEntity($billingAddress);

		if ($billingAddress->getStreet() === null) {
			$billingAddressData->companyCustomer = $order->getCompanyNumber() !== null;
			$billingAddressData->companyName = $order->getCompanyName();
			$billingAddressData->companyNumber = $order->getCompanyNumber();
			$billingAddressData->companyTaxNumber = $order->getCompanyTaxNumber();
			$billingAddressData->street = $order->getStreet();
			$billingAddressData->city = $order->getCity();
			$billingAddressData->postcode = $order->getPostcode();
			$billingAddressData->country = $order->getCountry();
		}

		if ($billingAddress->getTelephone() === null) {
			$billingAddressData->telephone = $order->getTelephone();
		}

		return $billingAddressData;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress|null $deliveryAddress
	 * @return \SS6\ShopBundle\Model\Customer\DeliveryAddressData
	 */
	private function getAmendedDeliveryAddressDataByOrder(Order $order, DeliveryAddress $deliveryAddress = null) {
		$deliveryAddressData = new DeliveryAddressData();

		if ($deliveryAddress === null) {
			$deliveryAddressData->addressFilled = !$order->isDeliveryAddressSameAsBillingAddress();
			$deliveryAddressData->street = $order->getDeliveryStreet();
			$deliveryAddressData->city = $order->getDeliveryCity();
			$deliveryAddressData->postcode = $order->getDeliveryPostcode();
			$deliveryAddressData->country = $order->getDeliveryCountry();
			$deliveryAddressData->companyName = $order->getDeliveryCompanyName();
			$deliveryAddressData->contactPerson = $order->getDeliveryContactPerson();
			$deliveryAddressData->telephone = $order->getDeliveryTelephone();
		} else {
			$deliveryAddressData->setFromEntity($deliveryAddress);
		}

		if ($deliveryAddress !== null && $deliveryAddress->getTelephone() === null) {
			$deliveryAddressData->telephone = $order->getTelephone();
		}

		return $deliveryAddressData;
	}

}
