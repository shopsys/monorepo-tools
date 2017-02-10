<?php

namespace Shopsys\ShopBundle\Model\Mail;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;

class MailTemplateRepository {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getMailTemplateRepository() {
        return $this->em->getRepository(MailTemplate::class);
    }

    /**
     * @param string $templateName
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate|null
     */
    public function findByNameAndDomainId($templateName, $domainId) {
        $criteria = ['name' => $templateName, 'domainId' => $domainId];

        return $this->getMailTemplateRepository()->findOneBy($criteria);
    }

    /**
     * @param string $templateName
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate
     */
    public function getByNameAndDomainId($templateName, $domainId) {
        $mailTemplate = $this->findByNameAndDomainId($templateName, $domainId);
        if ($mailTemplate === null) {
            $message = 'E-mail template with name "' . $templateName . '" was not found on domain with ID ' . $domainId . '.';
            throw new \Shopsys\ShopBundle\Model\Mail\Exception\MailTemplateNotFoundException($message);
        }

        return $mailTemplate;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Mail\MailTemplate[]
     */
    public function getAllByDomainId($domainId) {
        $criteria = ['domainId' => $domainId];
        return $this->getMailTemplateRepository()->findBy($criteria);
    }

}
