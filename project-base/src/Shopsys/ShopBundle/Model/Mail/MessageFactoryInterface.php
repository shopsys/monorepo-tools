<?php

namespace Shopsys\ShopBundle\Model\Mail;

interface MessageFactoryInterface {

	/**
	 * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate $template
	 * @param mixed $data
	 * @return \Shopsys\ShopBundle\Model\Mail\MessageData
	 */
	public function createMessage(MailTemplate $template, $data);

}
