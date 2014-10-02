<?php

namespace SS6\ShopBundle\Form\Admin\Order\Status;

class OrderStatusMailTemplatesData {

	/**
	 * @var \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	private $templates;

	/**
	 * @return \SS6\ShopBundle\Model\Mail\MailTemplateData[]
	 */
	public function getTemplates() {
		return $this->templates;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplateData[] $templates
	 */
	public function setTemplates($templates) {
		$this->templates = $templates;
	}

}
