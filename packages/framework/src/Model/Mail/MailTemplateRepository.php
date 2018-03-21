<?php

namespace Shopsys\FrameworkBundle\Model\Mail;

use Doctrine\ORM\EntityManagerInterface;

class MailTemplateRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getMailTemplateRepository()
    {
        return $this->em->getRepository(MailTemplate::class);
    }

    /**
     * @param string $templateName
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate|null
     */
    public function findByNameAndDomainId($templateName, $domainId)
    {
        $criteria = ['name' => $templateName, 'domainId' => $domainId];

        return $this->getMailTemplateRepository()->findOneBy($criteria);
    }

    /**
     * @param string $templateName
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate
     */
    public function getByNameAndDomainId($templateName, $domainId)
    {
        $mailTemplate = $this->findByNameAndDomainId($templateName, $domainId);
        if ($mailTemplate === null) {
            $message = 'E-mail template with name "' . $templateName . '" was not found on domain with ID ' . $domainId . '.';
            throw new \Shopsys\FrameworkBundle\Model\Mail\Exception\MailTemplateNotFoundException($message);
        }

        return $mailTemplate;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Mail\MailTemplate[]
     */
    public function getAllByDomainId($domainId)
    {
        $criteria = ['domainId' => $domainId];
        return $this->getMailTemplateRepository()->findBy($criteria);
    }

    /**
     * @return bool
     */
    public function existsTemplateWithEnabledSendingHavingEmptyBodyOrSubject()
    {
        $countOfEmptyTemplates = (int)$this->em->createQueryBuilder()
            ->select('COUNT(mt)')
            ->from(MailTemplate::class, 'mt')
            ->where('mt.sendMail = TRUE')
            ->andWhere('mt.body IS NULL OR mt.subject IS NULL')
            ->getQuery()
            ->getSingleScalarResult();

        return $countOfEmptyTemplates > 0;
    }
}
