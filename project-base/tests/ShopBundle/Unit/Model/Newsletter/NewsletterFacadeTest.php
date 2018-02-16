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
    private $repository;

    /**
     * @var NewsletterFacade
     */
    private $facade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->em = $this->createMock(EntityManager::class);
        $this->repository = $this->createMock(NewsletterRepository::class);
        $this->facade = new NewsletterFacade($this->em, $this->repository);
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

        $this->facade->addSubscribedEmail('no-reply@shopsys.com', 1);
    }
}
