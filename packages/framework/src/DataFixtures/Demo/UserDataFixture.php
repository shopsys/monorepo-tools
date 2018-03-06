<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
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
            BaseSettingValueDataFixture::class,
            CountryDataFixture::class,
        ];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $customer
     */
    private function resetPassword(User $customer)
    {
        $customerPasswordService = $this->get(CustomerPasswordService::class);
        /* @var $customerPasswordService \Shopsys\FrameworkBundle\Model\Customer\CustomerPasswordService */
        $em = $this->get('doctrine.orm.entity_manager');
        /* @var $em \Doctrine\ORM\EntityManager */

        $customerPasswordService->resetPassword($customer);
        $em->flush($customer);
    }
}
