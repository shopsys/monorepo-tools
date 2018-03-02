<?php

namespace Tests\ShopBundle\Database\Model\Newsletter\NewsletterRepository;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use PHPUnit\Framework\Assert;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class GetAllEmailsDataIteratorMethodTest extends DatabaseTestCase
{
    const FIRST_DOMAIN_SUBSCRIBER_EMAIL = 'james.black@no-reply.com';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterRepository
     */
    private $newsletterRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->newsletterRepository = $this->getServiceByType(NewsletterRepository::class);
    }

    public function testSubscriberFoundInFirstDomain(): void
    {
        $iterator = $this->newsletterRepository->getAllEmailsDataIteratorByDomainId(1);
        $this->assertContainsNewsletterSubscriber($iterator, self::FIRST_DOMAIN_SUBSCRIBER_EMAIL);
    }

    public function testSubscriberNotFoundInSecondDomain(): void
    {
        $iterator = $this->newsletterRepository->getAllEmailsDataIteratorByDomainId(2);
        $this->assertNotContainsNewsletterSubscriber($iterator, self::FIRST_DOMAIN_SUBSCRIBER_EMAIL);
    }

    /**
     * @param \Doctrine\ORM\Internal\Hydration\IterableResult $iterator
     * @param string $email
     */
    private function assertContainsNewsletterSubscriber(IterableResult $iterator, string $email): void
    {
        foreach ($iterator as $row) {
            if ($row[0]['email'] === $email) {
                return;
            }
        }

        Assert::fail('Newsletter subscriber was not found, but was expected');
    }

    /**
     * @param \Doctrine\ORM\Internal\Hydration\IterableResult $iterator
     * @param string $email
     */
    private function assertNotContainsNewsletterSubscriber(IterableResult $iterator, string $email): void
    {
        foreach ($iterator as $row) {
            if ($row[0]['email'] === $email) {
                Assert::fail('Newsletter subscriber was found, but was not expected');
            }
        }
    }
}
