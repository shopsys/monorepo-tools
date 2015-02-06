<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MessageData;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;
use SS6\ShopBundle\Model\Setting\Setting;

class ResetPasswordMailService {

	const VARIABLE_EMAIL = '{email}';
	const VARIABLE_NEW_PASSWORD_URL = '{new_password_url}';

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	public function __construct(Setting $setting, DomainRouterFactory $domainRouterFactory) {
		$this->setting = $setting;
		$this->domainRouterFactory = $domainRouterFactory;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @return \SS6\ShopBundle\Model\Mail\MessageData
	 */
	public function getMessageData(User $user, MailTemplate $mailTemplate) {
		return new MessageData(
			$user->getEmail(),
			$mailTemplate->getBody(),
			$mailTemplate->getSubject(),
			$this->setting->get(MailSetting::MAIN_ADMIN_MAIL, $user->getDomainId()),
			$this->setting->get(MailSetting::MAIN_ADMIN_MAIL_NAME, $user->getDomainId()),
			$this->getVariablesReplacements($user)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return array
	 */
	private function getVariablesReplacements(User $user) {
		return [
			self::VARIABLE_EMAIL => $user->getEmail(),
			self::VARIABLE_NEW_PASSWORD_URL => $this->getVariableNewPasswordUrl($user),
		];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return string
	 */
	private function getVariableNewPasswordUrl(User $user) {
		$router = $this->domainRouterFactory->getRouter($user->getDomainId());

		$routeParameters = [
			'email' => $user->getEmail(),
			'hash' => $user->getResetPasswordHash(),
		];

		return $router->generate('front_registration_set_new_password', $routeParameters, true);
	}

	/**
	 * @return array
	 */
	public function getTemplateVariables() {
		return [
			self::VARIABLE_EMAIL,
			self::VARIABLE_NEW_PASSWORD_URL,
		];
	}

}