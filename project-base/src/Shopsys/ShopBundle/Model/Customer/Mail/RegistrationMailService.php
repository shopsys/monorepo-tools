<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MessageData;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;

class RegistrationMailService {

	const VARIABLE_FIRST_NAME = '{first_name}';
	const VARIABLE_LAST_NAME = '{last_name}';
	const VARIABLE_EMAIL = '{email}';
	const VARIABLE_URL = '{url}';
	const VARIABLE_LOGIN_PAGE = '{login_page}';

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
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
	public function getMessageDataByUser(User $user, MailTemplate $mailTemplate) {
		return new MessageData(
			$user->getEmail(),
			$mailTemplate->getBccEmail(),
			$mailTemplate->getBody(),
			$mailTemplate->getSubject(),
			$this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL, $user->getDomainId()),
			$this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $user->getDomainId()),
			$this->getVariablesReplacements($user)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return array
	 */
	private function getVariablesReplacements(User $user) {
		$router = $this->domainRouterFactory->getRouter($user->getDomainId());

		return [
			self::VARIABLE_FIRST_NAME => $user->getFirstName(),
			self::VARIABLE_LAST_NAME => $user->getLastName(),
			self::VARIABLE_EMAIL => $user->getEmail(),
			self::VARIABLE_URL => $router->generate('front_homepage', [], true),
			self::VARIABLE_LOGIN_PAGE => $router->generate('front_login', [], true),
		];
	}

	/**
	 * @return array
	 */
	public function getTemplateVariables() {
		return [
			self::VARIABLE_FIRST_NAME,
			self::VARIABLE_LAST_NAME,
			self::VARIABLE_EMAIL,
			self::VARIABLE_URL,
			self::VARIABLE_LOGIN_PAGE,
		];
	}
}
