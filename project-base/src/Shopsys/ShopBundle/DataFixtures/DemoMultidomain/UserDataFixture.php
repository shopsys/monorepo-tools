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
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $customerFacade = $this->get(CustomerFacade::class);
        /* @var $customerFacade \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade */
        $loaderService = $this->get(UserDataFixtureLoader::class);
        /* @var $loaderService \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader */
        $faker = $this->get(Generator::class);
        /* @var $faker \Faker\Generator */

        $countries = [
            $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2),
            $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_2),
        ];
        $loaderService->injectReferences($countries);

        $customersData = $loaderService->getCustomersDataByDomainId(2);

        foreach ($customersData as $customerData) {
            $customerData->userData->createdAt = $faker->dateTimeBetween('-1 week', 'now');

            $customerFacade->create($customerData);
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
