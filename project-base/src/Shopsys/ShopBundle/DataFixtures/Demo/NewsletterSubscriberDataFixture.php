<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade;

class NewsletterSubscriberDataFixture extends AbstractReferenceFixture
{
    const FIRST_DOMAIN_ID = 1;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $newsletterFacade = $this->get(NewsletterFacade::class);
        /* @var $newsletterFacade \Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade */

        $newsletterSubscribersData = $this->getEmailData();

        foreach ($newsletterSubscribersData as $email) {
            $newsletterFacade->addSubscribedEmail($email, self::FIRST_DOMAIN_ID);
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
        ];
    }
}
