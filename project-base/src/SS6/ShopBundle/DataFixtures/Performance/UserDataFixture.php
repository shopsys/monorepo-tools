<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Faker\Generator as Faker;
use SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use SS6\ShopBundle\Component\Doctrine\EntityManagerFacade;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerFacade;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\UserDataFactory;

class UserDataFixture {

	const USERS_ON_EACH_DOMAIN = 100;
	const FIRST_PERFORMANCE_USER = 'first_performance_user';

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\EntityManagerFacade
	 */
	private $entityManagerFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade
	 */
	private $sqlLoggerFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerFacade
	 */
	private $customerEditFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserDataFactory
	 */
	private $userDataFactory;

	/**
	 * @var \Faker\Generator
	 */
	private $faker;

	/**
	 * @var \SS6\ShopBundle\Component\DataFixture\PersistentReferenceFacade
	 */
	private $persistentReferenceFacade;

	public function __construct(
		EntityManagerFacade $entityManagerFacade,
		Domain $domain,
		SqlLoggerFacade $sqlLoggerFacade,
		CustomerFacade $customerEditFacade,
		UserDataFactory $userDataFactory,
		Faker $faker,
		PersistentReferenceFacade $persistentReferenceFacade
	) {
		$this->entityManagerFacade = $entityManagerFacade;
		$this->domain = $domain;
		$this->sqlLoggerFacade = $sqlLoggerFacade;
		$this->customerEditFacade = $customerEditFacade;
		$this->userDataFactory = $userDataFactory;
		$this->faker = $faker;
		$this->persistentReferenceFacade = $persistentReferenceFacade;
	}

	public function load() {
		// Sql logging during mass data import makes memory leak
		$this->sqlLoggerFacade->temporarilyDisableLogging();

		$isFirstUser = true;

		foreach ($this->domain->getAll() as $domainConfig) {
			for ($i = 0; $i <  self::USERS_ON_EACH_DOMAIN; $i++) {
				$user = $this->createCustomerOnDomain($domainConfig->getId(), $i);

				if ($isFirstUser) {
					$this->persistentReferenceFacade->persistReference(self::FIRST_PERFORMANCE_USER, $user);
					$isFirstUser = false;
				}

				$this->entityManagerFacade->clear();
			}
		}

		$this->sqlLoggerFacade->reenableLogging();
	}

	/**
	 * @param int $domainId
	 * @param int $userNumber
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function createCustomerOnDomain($domainId, $userNumber) {
		$customerData = $this->getRandomCustomerDataByDomainId($domainId, $userNumber);

		return $this->customerEditFacade->create($customerData);
	}

	/**
	 * @param int $domainId
	 * @param int $userNumber
	 * @return \SS6\ShopBundle\Model\Customer\CustomerData
	 */
	private function getRandomCustomerDataByDomainId($domainId, $userNumber) {
		$customerData = new CustomerData();

		$country = $this->persistentReferenceFacade->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);

		$userData = $this->userDataFactory->createDefault($domainId);
		$userData->firstName = $this->faker->firstName;
		$userData->lastName = $this->faker->lastName;
		$userData->email = $userNumber . '.' . $this->faker->safeEmail;
		$userData->password = $this->faker->password;
		$userData->domainId = $domainId;
		$customerData->userData = $userData;

		$billingAddressData = new BillingAddressData();
		$billingAddressData->companyCustomer = $this->faker->boolean();
		if ($billingAddressData->companyCustomer === true) {
			$billingAddressData->companyName = $this->faker->company;
			$billingAddressData->companyNumber = $this->faker->randomNumber(6);
			$billingAddressData->companyTaxNumber = $this->faker->randomNumber(6);
		}
		$billingAddressData->street = $this->faker->streetAddress;
		$billingAddressData->city = $this->faker->city;
		$billingAddressData->postcode = $this->faker->postcode;
		$billingAddressData->country = $country;
		$billingAddressData->telephone = $this->faker->phoneNumber;
		$customerData->billingAddressData = $billingAddressData;

		$deliveryAddressData = new DeliveryAddressData();
		$deliveryAddressData->addressFilled = true;
		$deliveryAddressData->city = $this->faker->city;
		$deliveryAddressData->companyName = $this->faker->company;
		$deliveryAddressData->contactPerson = $this->faker->name;
		$deliveryAddressData->postcode = $this->faker->postcode;
		$deliveryAddressData->country = $country;
		$deliveryAddressData->street = $this->faker->streetAddress;
		$deliveryAddressData->telephone = $this->faker->phoneNumber;
		$customerData->deliveryAddressData = $deliveryAddressData;

		return $customerData;
	}

}
