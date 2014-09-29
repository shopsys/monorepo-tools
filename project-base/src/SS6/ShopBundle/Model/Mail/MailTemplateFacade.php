<?php

namespace SS6\ShopBundle\Model\Mail;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Mail\MailTemplateRepository;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

class MailTemplateFacade {

	const TEMPLATE_NAME_PREFIX = 'order_status_';

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateRepository
	 */
	private $mailTemplateRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateRepository $mailTemplateRepository
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
	 */
	public function __construct(
		EntityManager $em,
		MailTemplateRepository $mailTemplateRepository,
		OrderStatusRepository $orderStatusRepository
		) {
		$this->em = $em;
		$this->mailTemplateRepository = $mailTemplateRepository;
		$this->orderStatusRepository = $orderStatusRepository;
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

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 */
	public function edit(MailTemplate $mailTemplate, MailTemplateData $mailTemplateData) {
		$mailTemplate->edit($mailTemplateData);
		$this->em->flush();
	}

	public function prepareAllTemplates() {
		$orderStatuses = $this->orderStatusRepository->findAll();
		foreach ($orderStatuses as $orderStatus) {
			$templateName = $this::TEMPLATE_NAME_PREFIX . $orderStatus->getId();
			if ($this->find($templateName) === null) {
				$this->create($templateName);
			}
		}
	}

	/**
	 * @param type $name
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $mailTemplateData
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function create($name, MailTemplateData $mailTemplateData = null) {
		$mailTemplate = new MailTemplate($name, $mailTemplateData);
		$this->em->persist($mailTemplate);
		$this->em->flush();

		return $mailTemplate;
	}
}
