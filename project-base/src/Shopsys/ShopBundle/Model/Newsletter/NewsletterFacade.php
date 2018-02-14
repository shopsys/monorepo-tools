<?php

namespace Shopsys\ShopBundle\Model\Newsletter;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;

class NewsletterFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Newsletter\NewsletterRepository
     */
    private $newsletterRepository;

    public function __construct(
        EntityManager $em,
        NewsletterRepository $newsletterRepository
    ) {
        $this->em = $em;
        $this->newsletterRepository = $newsletterRepository;
    }

    /**
     * @param string $email
     */
    public function addSubscribedEmail($email)
    {
        if (!$this->newsletterRepository->existsSubscribedEmail($email)) {
            $newsletterSubscriber = new NewsletterSubscriber($email, new DateTimeImmutable());
            $this->em->persist($newsletterSubscriber);
            $this->em->flush($newsletterSubscriber);
        }
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getAllEmailsDataIterator()
    {
        return $this->newsletterRepository->getAllEmailsDataIterator();
    }
}
