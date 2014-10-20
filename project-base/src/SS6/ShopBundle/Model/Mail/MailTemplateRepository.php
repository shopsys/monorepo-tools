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
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate[]
	 */
	public function getAll() {
		return $this->getMailTemplateRepository()->findAll();
	}

	/**
	 * @param string $templateName
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate|null
	 */
	public function findByName($templateName) {
		$criteria = ['name' => $templateName];

		return $this->getMailTemplateRepository()->findOneBy($criteria);
	}

	/**
	 * @param string $templateName
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 * @throws \SS6\ShopBundle\Model\Mail\Exception\MailTemplateNotFoundException
	 */
	public function getByName($templateName) {
		$mailTemplate = $this->findByName($templateName);
		if ($mailTemplate === null) {
			$message = 'E-mail template with name "' . $templateName . '" was not found.';
			throw new \SS6\ShopBundle\Model\Mail\Exception\MailTemplateNotFoundException($message);
		}

		return $mailTemplate;
	}

}
