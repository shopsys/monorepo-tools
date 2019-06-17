<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Model\Customer;

use Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class CustomerFacadeTest extends TransactionFunctionalTestCase
{
    protected const EXISTING_EMAIL_ON_DOMAIN_1 = 'no-reply.3@shopsys.com';
    protected const EXISTING_EMAIL_ON_DOMAIN_2 = 'no-reply.4@shopsys.com';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    protected $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerDataFactory
     */
    protected $customerDataFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->customerFacade = $this->getContainer()->get(CustomerFacade::class);
        $this->customerDataFactory = $this->getContainer()->get(CustomerDataFactoryInterface::class);
    }

    public function testChangeEmailToExistingEmailButDifferentDomainDoNotThrowException()
    {
        $user = $this->customerFacade->findUserByEmailAndDomain(self::EXISTING_EMAIL_ON_DOMAIN_1, 1);
        $customerData = $this->customerDataFactory->createFromUser($user);
        $customerData->userData->email = self::EXISTING_EMAIL_ON_DOMAIN_2;

        $this->customerFacade->editByAdmin($user->getId(), $customerData);

        $this->expectNotToPerformAssertions();
    }

    public function testCreateNotDuplicateEmail()
    {
        $customerData = $this->customerDataFactory->create();
        $customerData->userData->pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);
        $customerData->userData->domainId = 1;
        $customerData->userData->email = 'unique-email@shopsys.com';
        $customerData->userData->firstName = 'John';
        $customerData->userData->lastName = 'Doe';

        $this->customerFacade->create($customerData);

        $this->expectNotToPerformAssertions();
    }

    public function testCreateDuplicateEmail()
    {
        $user = $this->customerFacade->findUserByEmailAndDomain(self::EXISTING_EMAIL_ON_DOMAIN_1, 1);
        $customerData = $this->customerDataFactory->createFromUser($user);
        $this->expectException(\Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException::class);

        $this->customerFacade->create($customerData);
    }

    public function testCreateDuplicateEmailCaseInsentitive()
    {
        $user = $this->customerFacade->findUserByEmailAndDomain(self::EXISTING_EMAIL_ON_DOMAIN_1, 1);
        $customerData = $this->customerDataFactory->createFromUser($user);
        $customerData->userData->email = mb_strtoupper(self::EXISTING_EMAIL_ON_DOMAIN_1);
        $this->expectException(\Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException::class);

        $this->customerFacade->create($customerData);
    }
}
