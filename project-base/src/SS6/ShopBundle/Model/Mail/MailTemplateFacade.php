<?php

namespace SS6\ShopBundle\Model\Mail;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Mail\MailTemplateRepository;
use SS6\ShopBundle\Model\Mail\AllMailTemplatesData;
use SS6\ShopBundle\Model\Order\Status\OrderStatusMailTemplateService;
use SS6\ShopBundle\Model\Order\Status\OrderStatusRepository;

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
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository
	 */
	private $orderStatusRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Order\Status\OrderStatusMailTemplateService
	 */
	private $orderStatusMailTemplateService;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateRepository $mailTemplateRepository
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusRepository $orderStatusRepository
	 * @param \SS6\ShopBundle\Model\Order\Status\OrderStatusMailTemplateService $orderStatusMailTemplateService
	 */
	public function __construct(
		EntityManager $em,
		MailTemplateRepository $mailTemplateRepository,
		OrderStatusRepository $orderStatusRepository,
		OrderStatusMailTemplateService $orderStatusMailTemplateService
	) {
		$this->em = $em;
		$this->mailTemplateRepository = $mailTemplateRepository;
		$this->orderStatusRepository = $orderStatusRepository;
		$this->orderStatusMailTemplateService = $orderStatusMailTemplateService;
	}

	/**
	 * @param string $templateName
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function get($templateName, $domainId) {
		return $this->mailTemplateRepository->findByNameAndDomainId($templateName, $domainId);
	}

	/**
	 * @param string $templateName
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplate
	 */
	public function find($templateName) {
		return $this->mailTemplateRepository->findByName($templateName);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate[] $mailTemplatesData
	 * @param int $domainId
	 */
	public function saveMailTemplatesData(array $mailTemplatesData, $domainId) {
		foreach ($mailTemplatesData as $mailTemplateData) {
			$mailTemplate = $this->mailTemplateRepository->findByNameAndDomainId($mailTemplateData->getName(), $domainId);
			if ($mailTemplate !== null) {
				$mailTemplate->edit($mailTemplateData);
			} else {
				$mailTemplate = new MailTemplate($mailTemplateData->getName(), $domainId, $mailTemplateData);
				$this->em->persist($mailTemplate);
			}
		}

		$this->em->flush();
	}

	/**
	 * @return \SS6\ShopBundle\Model\Order\Status\OrderStatus[]
	 */
	public function getAllIndexedById() {
		return $this->orderStatusRepository->getAllIndexedById();
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

	/**
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Mail\AllMailTemplatesData
	 */
	public function getAllMailTemplatesDataByDomainId($domainId) {
		$orderStatuses = $this->orderStatusRepository->findAll();
		$mailTemplates = $this->mailTemplateRepository->getAllByDomainId($domainId);

		$allMailTemplatesData = new AllMailTemplatesData();

		$registrationMailTemplatesData = new MailTemplateData();
		$registrationMailTemplate = $this->mailTemplateRepository->findByNameAndDomainId('registration_confirm', $domainId);
		if ($registrationMailTemplate !== null) {
			$registrationMailTemplatesData->setFromEntity($registrationMailTemplate);
		}
		$registrationMailTemplatesData->setName('registration_confirm');

		$allMailTemplatesData->setRegistrationTemplate($registrationMailTemplatesData);

		$allMailTemplatesData->setOrderStatusTemplates(
			$this->orderStatusMailTemplateService->getOrderStatusMailTemplatesData($orderStatuses, $mailTemplates));

		return $allMailTemplatesData;
	}

}
