<?php

namespace Shopsys\ShopBundle\Model\Customer\Mail;

use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MessageData;
use Shopsys\ShopBundle\Model\Mail\Setting\MailSetting;

class RegistrationMailService {

    const VARIABLE_FIRST_NAME = '{first_name}';
    const VARIABLE_LAST_NAME = '{last_name}';
    const VARIABLE_EMAIL = '{email}';
    const VARIABLE_URL = '{url}';
    const VARIABLE_LOGIN_PAGE = '{login_page}';

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
     */
    private $domainRouterFactory;

    public function __construct(Setting $setting, DomainRouterFactory $domainRouterFactory) {
        $this->setting = $setting;
        $this->domainRouterFactory = $domainRouterFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate $mailTemplate
     * @return \Shopsys\ShopBundle\Model\Mail\MessageData
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
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
