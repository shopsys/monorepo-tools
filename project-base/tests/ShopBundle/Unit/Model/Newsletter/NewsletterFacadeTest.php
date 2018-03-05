<?php

namespace Tests\ShopBundle\Unit\Model\Newsletter;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterRepository;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterSubscriber;

class NewsletterFacadeTest extends TestCase
{
    /**
     * @var EntityManager|PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var NewsletterRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $newsletterRepository;

    /**
     * @var NewsletterFacade
     */
    private $newsletterFacade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManager::class);
        $this->newsletterRepository = $this->createMock(NewsletterRepository::class);
        $this->newsletterFacade = new NewsletterFacade($this->em, $this->newsletterRepository);
    }

    public function testAddSubscribedEmail(): void
    {
        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(NewsletterSubscriber::class));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->newsletterFacade->addSubscribedEmail('no-reply@shopsys.com', 1);
    }

    public function testDeleteSubscribedEmail(): void
    {
        $newsletterSubscriberInstance = $this->createMock(NewsletterSubscriber::class);

        $this->newsletterRepository
            ->expects($this->any())
            ->method('getNewsletterSubscriberById')
            ->willReturn($newsletterSubscriberInstance);

        $this->em
            ->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf(NewsletterSubscriber::class));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->newsletterFacade->deleteById(1);
    }
}
