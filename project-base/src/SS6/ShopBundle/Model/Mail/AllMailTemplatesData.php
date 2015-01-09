<?php

namespace SS6\ShopBundle\Model\Mail;

class AllMailTemplatesData {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public $orderStatusTemplates;

	/**
	 *
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData
	 */
	public $registrationTemplate;

	/**
	 * @var int
	 */
	public $domainId;

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public function getAllTemplates() {
		$allTemplates = $this->getOrderStatusTemplates();
		$allTemplates[] = $this->getRegistrationTemplate();
		return $allTemplates;
	}



}
