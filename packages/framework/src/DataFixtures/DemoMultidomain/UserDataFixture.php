<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /** @var \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader */
    private $loaderService;

    /** @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade */
    private $customerFacade;

    /** @var \Faker\Generator */
    private $faker;

    /**
     * @param \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader $loaderService
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade
     * @param \Faker\Generator $faker
     */
    public function __construct(
        UserDataFixtureLoader $loaderService,
        CustomerFacade $customerFacade,
        Generator $faker
    ) {
        $this->loaderService = $loaderService;
        $this->customerFacade = $customerFacade;
        $this->faker = $faker;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $countries = [
            $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2),
            $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_2),
        ];
        $this->loaderService->injectReferences($countries);

        $customersData = $this->loaderService->getCustomersDataByDomainId(2);

        foreach ($customersData as $customerData) {
            $customerData->userData->createdAt = $this->faker->dateTimeBetween('-1 week', 'now');

            $this->customerFacade->create($customerData);
        }
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            CountryDataFixture::class,
        ];
    }
}
