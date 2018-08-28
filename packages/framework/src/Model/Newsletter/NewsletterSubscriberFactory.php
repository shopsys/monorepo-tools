<?php

namespace Shopsys\FrameworkBundle\Model\Newsletter;

use DateTimeImmutable;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class NewsletterSubscriberFactory implements NewsletterSubscriberFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param string $email
     * @param DateTimeImmutable $createdAt
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterSubscriber
     */
    public function create(string $email, DateTimeImmutable $createdAt, int $domainId): NewsletterSubscriber
    {
        $classData = $this->entityNameResolver->resolve(NewsletterSubscriber::class);

        return new $classData($email, $createdAt, $domainId);
    }
}
