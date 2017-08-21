<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader;
use Shopsys\ShopBundle\DataFixtures\DemoMultidomain\CountryDataFixture;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $customerFacade = $this->get('shopsys.shop.customer.customer_facade');
        /* @var $customerFacade \Shopsys\ShopBundle\Model\Customer\CustomerFacade */
        $loaderService = $this->get('shopsys.shop.data_fixtures.user_data_fixture_loader');
        /* @var $loaderService \Shopsys\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader */
        $faker = $this->get('faker.generator');
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
