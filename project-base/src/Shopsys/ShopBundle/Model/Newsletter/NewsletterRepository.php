<?php

namespace Shopsys\ShopBundle\Model\Newsletter;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;

class NewsletterRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getNewsletterSubscriberRepository()
    {
        return $this->em->getRepository(NewsletterSubscriber::class);
    }

    /**
     * @param string $email
     * @param int $domainId
     * @return bool
     */
    public function existsSubscribedEmail($email, $domainId)
    {
        return $this->getNewsletterSubscriberRepository()->findOneBy(['email' => $email, 'domainId' => $domainId]) !== null;
    }

    /**
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getAllEmailsDataIterator()
    {
        $query = $this->getNewsletterSubscriberRepository()
            ->createQueryBuilder('ns')
            ->select('ns.email, ns.createdAt')
            ->getQuery();

        return $query->iterate(null, AbstractQuery::HYDRATE_SCALAR);
    }
}
