<?php

namespace SS6\ShopBundle\Model\Mail;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Mail\MailTemplateRepository;

class MailTemplateFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateRepository
	 */
	private $mailTemplateRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateRepository $mailTemplateRepository
	 */
	public function __construct(EntityManager $em, MailTemplateRepository $mailTemplateRepository) {
		$this->em = $em;
		$this->mailTemplateRepository = $mailTemplateRepository;
	}

	/**
	 * @param string $templateName
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function get($templateName) {
		return $this->mailTemplateRepository->getByName($templateName);
	}

	/**
	 * @param string $templateName
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function find($templateName) {
		return $this->mailTemplateRepository->findByName($templateName);
	}

	
}
