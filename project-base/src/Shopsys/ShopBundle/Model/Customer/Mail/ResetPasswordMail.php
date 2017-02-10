<?php

namespace Shopsys\ShopBundle\Model\Customer\Mail;

use Shopsys\ShopBundle\Component\Router\DomainRouterFactory;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Mail\MailTemplate;
use Shopsys\ShopBundle\Model\Mail\MailTypeInterface;
use Shopsys\ShopBundle\Model\Mail\MessageData;
use Shopsys\ShopBundle\Model\Mail\MessageFactoryInterface;
use Shopsys\ShopBundle\Model\Mail\Setting\MailSetting;

class ResetPasswordMail implements MailTypeInterface, MessageFactoryInterface
{

    const VARIABLE_EMAIL = '{email}';
    const VARIABLE_NEW_PASSWORD_URL = '{new_password_url}';

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\DomainRouterFactory
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
     * @param \Shopsys\ShopBundle\Model\Mail\MailTemplate $template
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \Shopsys\ShopBundle\Model\Mail\MessageData
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return string[variableName]
     */
    private function getBodyVariablesValues(User $user) {
        return [
            self::VARIABLE_EMAIL => $user->getEmail(),
            self::VARIABLE_NEW_PASSWORD_URL => $this->getVariableNewPasswordUrl($user),
        ];
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
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
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return string[variableName]
     */
    private function getSubjectVariablesValues(User $user) {
        return $this->getBodyVariablesValues($user);
    }

}
