<?php

namespace SS6\ShopBundle\DataFixtures\Performance;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Faker\Factory as FakerFactory;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerEditFacade;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\UserDataFactory;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const USERS_ON_EACH_DOMAIN = 100;

	/**
	 * @var \Faker\Generator
	 */
	private $faker;

	public function __construct() {
		$this->faker = FakerFactory::create();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
	 */
	public function load(ObjectManager $objectManager) {
		$em = $this->get(EntityManager::class);
		/* @var $em \Doctrine\ORM\EntityManager */
		$domain = $this->get(Domain::class);
		/* @var $domain \SS6\ShopBundle\Component\Domain\Domain */
		$sqlLoggerFacade = $this->get(SqlLoggerFacade::class);
		/* @var $sqlLoggerFacade \SS6\ShopBundle\Component\Doctrine\SqlLoggerFacade */

		// Sql logging during mass data import makes memory leak
		$sqlLoggerFacade->temporarilyDisableLogging($em);
		foreach ($domain->getAll() as $domainConfig) {
			for ($i = 0; $i <  self::USERS_ON_EACH_DOMAIN; $i++) {
				$this->createCustomerOnDomain($domainConfig->getId(), $i);
				$em->clear();
			}
		}
		$sqlLoggerFacade->reenableLogging($em);
	}

	/**
	 * @param int $domainId
	 * @param int $userNumber
	 */
	private function createCustomerOnDomain($domainId, $userNumber) {
		$customerEditFacade = $this->get(CustomerEditFacade::class);
		/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */
		$customerData = $this->getRandomCustomerDataByDomainId($domainId, $userNumber);
		$customerEditFacade->create($customerData);
	}

	/**
	 * @param int $domainId
	 * @param int $userNumber
	 * @return \SS6\ShopBundle\Model\Customer\CustomerData
	 */
	private function getRandomCustomerDataByDomainId($domainId, $userNumber) {
		$userDataFactory = $this->get(UserDataFactory::class);
		/* @var $userDataFactory \SS6\ShopBundle\Model\Customer\UserDataFactory */
		$customerData = new CustomerData();

		$userData = $userDataFactory->createDefault($domainId);
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
		$billingAddressData->telephone = $this->faker->phoneNumber;
		$customerData->billingAddressData = $billingAddressData;

		$deliveryAddressData = new DeliveryAddressData();
		$deliveryAddressData->addressFilled = true;
		$deliveryAddressData->city = $this->faker->city;
		$deliveryAddressData->companyName = $this->faker->company;
		$deliveryAddressData->contactPerson = $this->faker->name;
		$deliveryAddressData->postcode = $this->faker->postcode;
		$deliveryAddressData->street = $this->faker->streetAddress;
		$deliveryAddressData->telephone = $this->faker->phoneNumber;
		$customerData->deliveryAddressData = $deliveryAddressData;

		return $customerData;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [PricingGroupDataFixture::class];
	}

}
