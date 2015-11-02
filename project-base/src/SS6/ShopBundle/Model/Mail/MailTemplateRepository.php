<?php

namespace SS6\ShopBundle\Model\Mail;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Mail\MailTemplate;

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
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate|null
	 */
	public function findByNameAndDomainId($templateName, $domainId) {
		$criteria = ['name' => $templateName, 'domainId' => $domainId];

		return $this->getMailTemplateRepository()->findOneBy($criteria);
	}

	/**
	 * @param string $templateName
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function getByNameAndDomainId($templateName, $domainId) {
		$mailTemplate = $this->findByNameAndDomainId($templateName, $domainId);
		if ($mailTemplate === null) {
			$message = 'E-mail template with name "' . $templateName . '" was not found on domain with ID ' . $domainId . '.';
			throw new \SS6\ShopBundle\Model\Mail\Exception\MailTemplateNotFoundException($message);
		}

		return $mailTemplate;
	}

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate[]
	 */
	public function getAllByDomainId($domainId) {
		$criteria = ['domainId' => $domainId];
		return $this->getMailTemplateRepository()->findBy($criteria);
	}

}
