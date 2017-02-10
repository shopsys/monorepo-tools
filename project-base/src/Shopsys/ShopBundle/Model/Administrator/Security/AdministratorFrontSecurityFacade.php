<?php

namespace Shopsys\ShopBundle\Model\Administrator\Security;

use Shopsys\ShopBundle\Model\Administrator\Security\AdministratorUserProvider;
use Shopsys\ShopBundle\Model\Security\Roles;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\User\UserInterface;

class AdministratorFrontSecurityFacade
{

    // same as in security.yml
    const ADMINISTRATION_CONTEXT = 'administration';

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Security\AdministratorUserProvider
     */
    private $administratorUserProvider;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker
     */
    private $authorizationChecker;

    public function __construct(
        SessionInterface $session,
        AdministratorUserProvider $administratorUserProvider,
        AccessDecisionManagerInterface $accessDecisionManager,
        AuthorizationChecker $authorizationChecker
    ) {
        $this->session = $session;
        $this->administratorUserProvider = $administratorUserProvider;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @return bool
     */
    public function isAdministratorLogged() {
        try {
            $token = $this->getAdministratorToken();
        } catch (\Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException $e) {
            return false;
        }

        if (!$token->isAuthenticated()) {
            return false;
        }

        return $this->accessDecisionManager->decide($token, [Roles::ROLE_ADMIN]);
    }

    /**
     * @return bool
     */
    public function isAdministratorLoggedAsCustomer() {
        try {
            return $this->authorizationChecker->isGranted(Roles::ROLE_ADMIN_AS_CUSTOMER);
        } catch (\Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException $e) {
            return false;
        }
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Administrator\Administrator
     */
    public function getCurrentAdministrator() {
        if ($this->isAdministratorLogged()) {
            return $this->getAdministratorToken()->getUser();
        } else {
            $message = 'Administrator is not logged.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\AdministratorIsNotLoggedException($message);
        }
    }

    /**
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     * @see \Symfony\Component\Security\Http\Firewall\ContextListener::handle()
     */
    private function getAdministratorToken() {
        $serializedToken = $this->session->get('_security_' . self::ADMINISTRATION_CONTEXT);
        if ($serializedToken === null) {
            $message = 'Token not found.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException($message);
        }

        $token = unserialize($serializedToken);
        if (!$token instanceof TokenInterface) {
            $message = 'Token has invalid interface.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException($message);
        }
        $this->refreshUserInToken($token);

        return $token;
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @see \Symfony\Component\Security\Http\Firewall\ContextListener::handle()
     * @see \Symfony\Component\Security\Core\Authentication\Token\AbstractToken::setUser()
     */
    private function refreshUserInToken(TokenInterface $token) {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            $message = 'User in token must implement UserInterface.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException($message);
        }

        try {
            $freshUser = $this->administratorUserProvider->refreshUser($user);
        } catch (\Symfony\Component\Security\Core\Exception\UnsupportedUserException $e) {
            $message = 'AdministratorUserProvider does not support user in this token.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException($message, $e);
        } catch (\Symfony\Component\Security\Core\Exception\UsernameNotFoundException $e) {
            $message = 'Username not found.';
            throw new \Shopsys\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenException($message, $e);
        }

        $token->setUser($freshUser);
    }

}
