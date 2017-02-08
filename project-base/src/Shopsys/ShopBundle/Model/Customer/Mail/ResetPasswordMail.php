<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\MailTypeInterface;
use SS6\ShopBundle\Model\Mail\MessageData;
use SS6\ShopBundle\Model\Mail\MessageFactoryInterface;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;

class ResetPasswordMail implements MailTypeInterface, MessageFactoryInterface {

	const VARIABLE_EMAIL = '{email}';
	const VARIABLE_NEW_PASSWORD_URL = '{new_password_url}';

	/**
	 * @var \SS6\ShopBundle\Component\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	public function __construct(
		Setting $setting,
		DomainRouterFactory $domainRouterFactory
	) {
		$this->setting = $setting;
		$this->domainRouterFactory = $domainRouterFactory;
	}

	/**
	 * @return string[]
	 */
	public function getBodyVariables() {
		return [
			self::VARIABLE_EMAIL,
			self::VARIABLE_NEW_PASSWORD_URL,
		];
	}

	/**
	 * @return string[]
	 */
	public function getSubjectVariables() {
		return $this->getBodyVariables();
	}

	/**
	 * @return string[]
	 */
	public function getRequiredBodyVariables() {
		return [
			self::VARIABLE_NEW_PASSWORD_URL,
		];
	}

	/**
	 * @return string[]
	 */
	public function getRequiredSubjectVariables() {
		return [];
	}

	/**
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $template
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \SS6\ShopBundle\Model\Mail\MessageData
	 */
	public function createMessage(MailTemplate $template, $user) {
		return new MessageData(
			$user->getEmail(),
			$template->getBccEmail(),
			$template->getBody(),
			$template->getSubject(),
			$this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL, $user->getDomainId()),
			$this->setting->getForDomain(MailSetting::MAIN_ADMIN_MAIL_NAME, $user->getDomainId()),
			$this->getBodyVariablesValues($user),
			$this->getSubjectVariablesValues($user)
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return string[variableName]
	 */
	private function getBodyVariablesValues(User $user) {
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
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return string[variableName]
	 */
	private function getSubjectVariablesValues(User $user) {
		return $this->getBodyVariablesValues($user);
	}

}
