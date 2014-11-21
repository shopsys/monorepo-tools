<?php

namespace SS6\ShopBundle\Model\Mail;

class AllMailTemplatesData {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	private $orderStatusTemplates;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData
	 */
	private $registrationTemplate;

	/**
	 * @var int
	 */
	private $domainId;

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public function getOrderStatusTemplates() {
		return $this->orderStatusTemplates;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData[] $templates
	 */
	public function setOrderStatusTemplates($templates) {
		$this->orderStatusTemplates = $templates;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplateData
	 */
	public function getRegistrationTemplate() {
		return $this->registrationTemplate;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData $registrationTemplate
	 */
	public function setRegistrationTemplate($registrationTemplate) {
		$this->registrationTemplate = $registrationTemplate;
	}

	/**
	 * @return int
	 */
	public function getDomainId() {
		return $this->domainId;
	}

	/**
	 * @param int $domainId
	 */
	public function setDomainId($domainId) {
		$this->domainId = $domainId;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public function getAllTemplates() {
		$allTemplates = $this->getOrderStatusTemplates();
		$allTemplates[] = $this->getRegistrationTemplate();
		return $allTemplates;
	}



}
