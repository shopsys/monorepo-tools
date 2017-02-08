<?php

namespace SS6\ShopBundle\Form\Admin\Mail;

use SS6\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormType;
use SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail;

class AllMailTemplatesFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\Mail\ResetPasswordMail
	 */
	private $resetPasswordMail;

	public function __construct(ResetPasswordMail $resetPasswordMail) {
		$this->resetPasswordMail = $resetPasswordMail;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Mail\AllMailTemplatesFormType
	 */
	public function create() {
		return new AllMailTemplatesFormType(
			$this->resetPasswordMail
		);
	}

}
