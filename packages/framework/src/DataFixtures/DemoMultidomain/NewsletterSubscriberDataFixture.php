<?php

namespace Shopsys\FrameworkBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;

class NewsletterSubscriberDataFixture extends AbstractReferenceFixture
{
    const SECOND_DOMAIN_ID = 2;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $newsletterFacade = $this->get(NewsletterFacade::class);
        /* @var $newsletterFacade \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade */

        $newsletterSubscribersData = $this->getEmailData();

        foreach ($newsletterSubscribersData as $email) {
            $newsletterFacade->addSubscribedEmail($email, self::SECOND_DOMAIN_ID);
        }
    }

    /**
     * @return string[]
     */
    private function getEmailData()
    {
        return [
            'anna.anina@no-reply.com',
            'jonathan.anderson@no-reply.com',
            'peter.parkson@no-reply.com',
        ];
    }
}
