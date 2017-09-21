<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use Shopsys\ShopBundle\Model\Customer\User;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const USER_WITH_RESET_PASSWORD_HASH = 'user_with_reset_password_hash';

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
            $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1),
            $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1),
        ];
        $loaderService->injectReferences($countries);

        $customersData = $loaderService->getCustomersDataByDomainId(Domain::FIRST_DOMAIN_ID);

        foreach ($customersData as $customerData) {
            $customerData->userData->createdAt = $faker->dateTimeBetween('-1 week', 'now');

            $customer = $customerFacade->create($customerData);

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
            SettingValueDataFixture::class,
            CountryDataFixture::class,
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $customer
     */
    private function resetPassword(User $customer)
    {
        $customerPasswordService = $this->get('shopsys.shop.customer.customer_password_service');
        /* @var $customerPasswordService \Shopsys\ShopBundle\Model\Customer\CustomerPasswordService */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

        $customerPasswordService->resetPassword($customer);
        $em->flush($customer);
    }
}
