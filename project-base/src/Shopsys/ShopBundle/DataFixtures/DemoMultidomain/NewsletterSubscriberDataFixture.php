<?php

namespace Shopsys\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade;

class NewsletterSubscriberDataFixture extends AbstractReferenceFixture
{
    const SECOND_DOMAIN_ID = 2;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $newsletterFacade = $this->get(NewsletterFacade::class);

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
