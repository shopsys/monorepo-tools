<?php

namespace Shopsys\FrameworkBundle\Model\Newsletter;

use DateTimeImmutable;

class NewsletterSubscriberFactory implements NewsletterSubscriberFactoryInterface
{
    /**
     * @param string $email
     * @param DateTimeImmutable $createdAt
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterSubscriber
     */
    public function create(string $email, DateTimeImmutable $createdAt, int $domainId): NewsletterSubscriber
    {
        return new NewsletterSubscriber($email, $createdAt, $domainId);
    }
}
