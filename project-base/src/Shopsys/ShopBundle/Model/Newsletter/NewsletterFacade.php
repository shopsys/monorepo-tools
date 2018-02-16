<?php

namespace Shopsys\ShopBundle\Model\Newsletter;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData;

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
     * @param int $domainId
     */
    public function addSubscribedEmail($email, $domainId)
    {
        if (!$this->newsletterRepository->existsSubscribedEmail($email, $domainId)) {
            $newsletterSubscriber = new NewsletterSubscriber($email, new DateTimeImmutable(), $domainId);
            $this->em->persist($newsletterSubscriber);
            $this->em->flush($newsletterSubscriber);
        }
    }

    /**
     * @param int $domainId
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getAllEmailsDataIteratorByDomainId($domainId)
    {
        return $this->newsletterRepository->getAllEmailsDataIteratorByDomainId($domainId);
    }

    /**
     * @param int $selectedDomainId
     * @param \Shopsys\ShopBundle\Form\Admin\QuickSearch\QuickSearchFormData $searchData
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForQuickSearch(int $selectedDomainId, QuickSearchFormData $searchData)
    {
        return $this->newsletterRepository->getQueryBuilderForQuickSearch($selectedDomainId, $searchData);
    }

    /**
     * @param int $id
     * @return \Shopsys\ShopBundle\Model\Newsletter\NewsletterSubscriber
     */
    public function getNewsletterSubscriberById(int $id)
    {
        return $this->newsletterRepository->getNewsletterSubscriberById($id);
    }

    /**
     * @param int $id
     */
    public function deleteById(int $id)
    {
        $newsletterSubscriber = $this->getNewsletterSubscriberById($id);

        $this->em->remove($newsletterSubscriber);
        $this->em->flush();
    }
}
