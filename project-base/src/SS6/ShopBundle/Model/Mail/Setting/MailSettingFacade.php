<?php

namespace SS6\ShopBundle\Model\Mail\Setting;

use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;

class MailSettingFacade {

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @param \SS6\ShopBundle\Component\Setting\Setting $setting
	 */
	public function __construct(
		Setting $setting
	) {
		$this->setting = $setting;
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getMainAdminMail($domainId) {
		return $this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL, $domainId);
	}

	/**
	 * @param int $domainId
	 * @return string
	 */
	public function getMainAdminMailName($domainId) {
		return $this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $domainId);
	}

	/**
	 * @param string $mainAdminMail
	 * @param int $domainId
	 */
	public function setMainAdminMail($mainAdminMail, $domainId) {
		$this->setting->setForDomain(MailSetting::MAIN_ADMIN_MAIL, $mainAdminMail, $domainId);
	}

	/**
	 * @param string $mainAdminMailName
	 * @param int $domainId
	 */
	public function setMainAdminMailName($mainAdminMailName, $domainId) {
		$this->setting->setForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $mainAdminMailName, $domainId);
	}
}
