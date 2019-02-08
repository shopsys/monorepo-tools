<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\String\HashGenerator;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\User;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const USER_WITH_RESET_PASSWORD_HASH = 'user_with_reset_password_hash';

    /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade */
    protected $customerFacade;

    /** @var \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader */
    protected $loaderService;

    /** @var \Faker\Generator */
    protected $faker;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    protected $em;

    /** @var \Shopsys\FrameworkBundle\Component\String\HashGenerator */
    protected $hashGenerator;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader $loaderService
     * @param \Faker\Generator $faker
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Component\String\HashGenerator $hashGenerator
     */
    public function __construct(
        CustomerFacade $customerFacade,
        UserDataFixtureLoader $loaderService,
        Generator $faker,
        EntityManagerInterface $em,
        HashGenerator $hashGenerator
    ) {
        $this->customerFacade = $customerFacade;
        $this->loaderService = $loaderService;
        $this->faker = $faker;
        $this->em = $em;
        $this->hashGenerator = $hashGenerator;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $countries = [
            $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC),
            $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA),
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
            CountryDataFixture::class,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $customer
     */
    protected function resetPassword(User $customer)
    {
        $customer->resetPassword($this->hashGenerator);
        $this->em->flush($customer);
    }
}
