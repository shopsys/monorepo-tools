<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;

class MultidomainUserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    /**
     * @var \Shopsys\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader
     */
    protected $loaderService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    protected $customerFacade;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader $loaderService
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade
     * @param \Faker\Generator $faker
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
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
    protected function loadForDomain(int $domainId)
    {
        $countries = [
            $this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC),
            $this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA),
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
            UserDataFixture::class,
        ];
    }
}
