<?php

namespace SS6\ShopBundle\Model\Mail;

class AllMailTemplatesData {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public $orderStatusTemplates;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData
	 */
	public $registrationTemplate;

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData
	 */
	public $resetPasswordTemplate;

	/**
	 * @var int
	 */
	public $domainId;

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public function getAllTemplates() {
		$allTemplates = $this->orderStatusTemplates;
		$allTemplates[] = $this->registrationTemplate;
		$allTemplates[] = $this->resetPasswordTemplate;
		return $allTemplates;
	}

}
