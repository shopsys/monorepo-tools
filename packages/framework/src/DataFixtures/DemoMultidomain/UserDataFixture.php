<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\DataFixtures\Demo\UserDataFixtureLoader
     */
    private $loaderService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        UserDataFixtureLoader $loaderService,
        CustomerFacade $customerFacade,
        Generator $faker,
        Domain $domain
    ) {
        $this->loaderService = $loaderService;
        $this->customerFacade = $customerFacade;
        $this->faker = $faker;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    private function loadForDomain(int $domainId)
    {
        $countries = [
            $this->getReferenceForDomain(CountryDataFixture::COUNTRY_CZECH_REPUBLIC, $domainId),
            $this->getReferenceForDomain(CountryDataFixture::COUNTRY_SLOVAKIA, $domainId),
        ];
        $this->loaderService->injectReferences($countries);

        $customersData = $this->loaderService->getCustomersDataByDomainId($domainId);

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
