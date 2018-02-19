<?php

namespace Shopsys\ShopBundle\DataFixtures\Performance;

use Faker\Generator as Faker;
use Shopsys\ShopBundle\Component\Console\ProgressBarFactory;
use Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use Shopsys\ShopBundle\Model\Customer\BillingAddressData;
use Shopsys\ShopBundle\Model\Customer\CustomerData;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\Model\Customer\DeliveryAddressData;
use Shopsys\ShopBundle\Model\Customer\UserDataFactory;
use Symfony\Component\Console\Output\OutputInterface;

class UserDataFixture
{
    const FIRST_PERFORMANCE_USER = 'first_performance_user';

    /**
     * @var int
     */
    private $userCountPerDomain;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade
     */
    private $sqlLoggerFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerFacade
     */
    private $customerEditFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserDataFactory
     */
    private $userDataFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Console\ProgressBarFactory
     */
    private $progressBarFactory;

    /**
     * @param int $userCountPerDomain
     * @param \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade $entityManagerFacade
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     * @param \Shopsys\ShopBundle\Component\Doctrine\SqlLoggerFacade $sqlLoggerFacade
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerFacade $customerEditFacade
     * @param \Shopsys\ShopBundle\Model\Customer\UserDataFactory $userDataFactory
     * @param \Faker\Generator $faker
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Shopsys\ShopBundle\Component\Console\ProgressBarFactory $progressBarFactory
     */
    public function __construct(
        $userCountPerDomain,
        EntityManagerFacade $entityManagerFacade,
        Domain $domain,
        SqlLoggerFacade $sqlLoggerFacade,
        CustomerFacade $customerEditFacade,
        UserDataFactory $userDataFactory,
        Faker $faker,
        PersistentReferenceFacade $persistentReferenceFacade,
        ProgressBarFactory $progressBarFactory
    ) {
        $this->entityManagerFacade = $entityManagerFacade;
        $this->domain = $domain;
        $this->sqlLoggerFacade = $sqlLoggerFacade;
        $this->customerEditFacade = $customerEditFacade;
        $this->userDataFactory = $userDataFactory;
        $this->faker = $faker;
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->userCountPerDomain = $userCountPerDomain;
        $this->progressBarFactory = $progressBarFactory;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function load(OutputInterface $output)
    {
        // Sql logging during mass data import makes memory leak
        $this->sqlLoggerFacade->temporarilyDisableLogging();
        $domains = $this->domain->getAll();

        $progressBar = $this->progressBarFactory->create($output, count($domains) * $this->userCountPerDomain);

        $isFirstUser = true;

        foreach ($domains as $domainConfig) {
            for ($i = 0; $i < $this->userCountPerDomain; $i++) {
                $user = $this->createCustomerOnDomain($domainConfig->getId(), $i);
                $progressBar->advance();

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
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    private function createCustomerOnDomain($domainId, $userNumber)
    {
        $customerData = $this->getRandomCustomerDataByDomainId($domainId, $userNumber);

        return $this->customerEditFacade->create($customerData);
    }

    /**
     * @param int $domainId
     * @param int $userNumber
     * @return \Shopsys\ShopBundle\Model\Customer\CustomerData
     */
    private function getRandomCustomerDataByDomainId($domainId, $userNumber)
    {
        $customerData = new CustomerData();

        $country = $this->persistentReferenceFacade->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1);

        $userData = $this->userDataFactory->createDefault($domainId);
        $userData->firstName = $this->faker->firstName;
        $userData->lastName = $this->faker->lastName;
        $userData->email = $userNumber . '.' . $this->faker->safeEmail;
        $userData->password = $this->faker->password;
        $userData->domainId = $domainId;
        $userData->createdAt = $this->faker->dateTimeBetween('-1 year', 'now');

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
        $deliveryAddressData->firstName = $this->faker->firstName;
        $deliveryAddressData->lastName = $this->faker->lastName;
        $deliveryAddressData->postcode = $this->faker->postcode;
        $deliveryAddressData->country = $country;
        $deliveryAddressData->street = $this->faker->streetAddress;
        $deliveryAddressData->telephone = $this->faker->phoneNumber;
        $customerData->deliveryAddressData = $deliveryAddressData;

        return $customerData;
    }
}
