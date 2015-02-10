<?php

namespace SS6\ShopBundle\Model\Mail;

interface MessageFactoryInterface {

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $template
	 * @param mixed $data
	 * @return \SS6\ShopBundle\Model\Mail\MessageData
	 */
	public function createMessage(MailTemplate $template, $data);

}
