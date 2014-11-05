<?php

namespace SS6\ShopBundle\Model\Mail\Setting;

use SS6\ShopBundle\Model\Mail\Setting\MailSetting;
use SS6\ShopBundle\Model\Setting\Setting;

class MailSettingFacade {

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 */
	public function __construct(
		Setting $setting
	) {
		$this->setting = $setting;
	}

	/**
	 * @return string
	 */
	public function getMainAdminMail() {
		return $this->setting->get(MailSetting::MAIN_ADMIN_MAIL);
	}

	/**
	 * @return string
	 */
	public function getMainAdminMailName() {
		return $this->setting->get(MailSetting::MAIN_ADMIN_MAIL_NAME);
	}

	/**
	 * @param string $mainAdminMail
	 */
	public function setMainAdminMail($mainAdminMail) {
		$this->setting->set(MailSetting::MAIN_ADMIN_MAIL, $mainAdminMail);
	}

	/**
	 * @param string $mainAdminMailName
	 */
	public function setMainAdminMailNAme($mainAdminMailName) {
		$this->setting->set(MailSetting::MAIN_ADMIN_MAIL_NAME, $mainAdminMailName);
	}
}
