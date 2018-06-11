<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;

class NewsletterSubscriberDataFixture extends AbstractReferenceFixture
{
    const FIRST_DOMAIN_ID = 1;

    /** @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade */
    private $newsletterFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade $newsletterFacade
     */
    public function __construct(NewsletterFacade $newsletterFacade)
    {
        $this->newsletterFacade = $newsletterFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $newsletterSubscribersData = $this->getEmailData();

        foreach ($newsletterSubscribersData as $email) {
            $this->newsletterFacade->addSubscribedEmail($email, self::FIRST_DOMAIN_ID);
        }
    }

    /**
     * @return string[]
     */
    private function getEmailData()
    {
        return [
            'james.black@no-reply.com',
            'johny.good@no-reply.com',
            'andrew.mathewson@no-reply.com',
            'vitek@shopsys.com',
        ];
    }
}
