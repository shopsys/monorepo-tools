<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Base\SettingValueDataFixture as BaseSettingValueDataFixture;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService;
use Shopsys\FrameworkBundle\Model\Customer\User;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const USER_WITH_RESET_PASSWORD_HASH = 'user_with_reset_password_hash';

    /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade */
    private $customerFacade;

    /** @var \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader */
    private $loaderService;

    /** @var \Faker\Generator */
    private $faker;

    /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService */
    private $customerPasswordService;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $em;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader $loaderService
     * @param \Faker\Generator $faker
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService $customerPasswordService
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     */
    public function __construct(
        CustomerFacade $customerFacade,
        UserDataFixtureLoader $loaderService,
        Generator $faker,
        CustomerPasswordService $customerPasswordService,
        EntityManagerInterface $em
    ) {
        $this->customerFacade = $customerFacade;
        $this->loaderService = $loaderService;
        $this->faker = $faker;
        $this->customerPasswordService = $customerPasswordService;
        $this->em = $em;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $countries = [
            $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1),
            $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1),
        ];
        $this->loaderService->injectReferences($countries);

        $customersData = $this->loaderService->getCustomersDataByDomainId(Domain::FIRST_DOMAIN_ID);

        foreach ($customersData as $customerData) {
            $customerData->userData->createdAt = $this->faker->dateTimeBetween('-1 week', 'now');

            $customer = $this->customerFacade->create($customerData);

            if ($customer->getId() === 1) {
                $this->resetPassword($customer);
                $this->addReference(self::USER_WITH_RESET_PASSWORD_HASH, $customer);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            BaseSettingValueDataFixture::class,
            CountryDataFixture::class,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $customer
     */
    private function resetPassword(User $customer)
    {
        $this->customerPasswordService->resetPassword($customer);
        $this->em->flush($customer);
    }
}
