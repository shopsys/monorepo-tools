<?php

namespace Tests\ShopBundle\Database\Model\Newsletter;

use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterSubscriber;
use Tests\ShopBundle\Test\DatabaseTestCase;

class NewsletterSubscriberPersistenceTest extends DatabaseTestCase
{
    public function testPersistence(): void
    {
        $newsletterSubscriber = new NewsletterSubscriber(
            'no-reply@shopsys.com',
            new DateTimeImmutable('2018-02-06 15:15:48')
        );

        $em = $this->getEntityManager();
        $em->persist($newsletterSubscriber);
        $em->flush();
        $em->clear();

        $found = $em->find(NewsletterSubscriber::class, 'no-reply@shopsys.com');
        Assert::assertEquals($newsletterSubscriber, $found);
        Assert::assertNotSame($newsletterSubscriber, $found);
    }
}
