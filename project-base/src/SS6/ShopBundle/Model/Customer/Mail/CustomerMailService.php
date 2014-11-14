<?php

namespace SS6\ShopBundle\Model\Customer\Mail;

use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Mail\MailTemplate;
use SS6\ShopBundle\Model\Mail\Setting\MailSetting;
use SS6\ShopBundle\Model\Setting\Setting;
use Swift_Message;
use Symfony\Cmf\Component\Routing\ChainRouter;

class CustomerMailService {

	const VARIABLE_FIRST_NAME = '{first_name}';
	const VARIABLE_LAST_NAME = '{last_name}';
	const VARIABLE_EMAIL = '{email}';
	const VARIABLE_URL = '{url}';
	const VARIABLE_LOGIN_PAGE = '{login_page}';

	/**
	 * @var \SS6\ShopBundle\Model\Setting\Setting
	 */
	private $setting;

	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @param \SS6\ShopBundle\Model\Setting\Setting $setting
	 * @param \Symfony\Cmf\Component\Routing\ChainRouter $router
	 */
	public function __construct(Setting $setting, ChainRouter $router) {
		$this->setting = $setting;
		$this->router = $router;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @param \SS6\ShopBundle\Model\Mail\MailTemplate $mailTemplate
	 * @return \Swift_Message
	 */
	public function getMessageByUser(User $user, MailTemplate $mailTemplate) {
		$toEmail = $user->getEmail();
		$body = $this->transformVariables(
			$mailTemplate->getBody(),
			$user
		);
		$subject = $this->transformVariables(
			$mailTemplate->getSubject(),
			$user
		);

		$message = Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom(
				$this->setting->get(MailSetting::MAIN_ADMIN_MAIL, $user->getDomainId()),
				$this->setting->get(MailSetting::MAIN_ADMIN_MAIL_NAME, $user->getDomainId())
			)
			->setTo($toEmail)
			->setContentType('text/plain; charset=UTF-8')
			->setBody(strip_tags($body), 'text/plain')
			->addPart($body, 'text/html');

		return $message;
	}

	/**
	 * @param string $string
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return string
	 */
	private function transformVariables($string, User $user) {
		$variableKeys = array_keys($this->getRegistrationTemplateVariables());
		$variableValues = array(
			self::VARIABLE_FIRST_NAME => $user->getFirstName(),
			self::VARIABLE_LAST_NAME => $user->getLastName(),
			self::VARIABLE_EMAIL => $user->getEmail(),
			self::VARIABLE_URL => $this->router->generate('front_homepage', array(), true),
			self::VARIABLE_LOGIN_PAGE => $this->router->generate('front_login', array(), true),
		);
		foreach ($variableKeys as $key) {
			$string = str_replace($key, $variableValues[$key], $string);
		}

		return $string;
	}

	/**
	 * @return array
	 */
	public function getRegistrationTemplateVariables() {
		return array(
			self::VARIABLE_FIRST_NAME => 'Jméno',
			self::VARIABLE_LAST_NAME => 'Příjmení',
			self::VARIABLE_EMAIL => 'Email',
			self::VARIABLE_URL => 'URL adresa e-shopu',
			self::VARIABLE_LOGIN_PAGE => 'Odkaz na stránku s přihlášením',
		);
	}
}