<?php

namespace SS6\ShopBundle\Model\Administrator\Security;

use SS6\ShopBundle\Model\Administrator\Security\AdministratorUserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdministratorSecurityFacade {

	// same as in security.yml
	const ADMINISTRATION_CONTEXT = 'administration';

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	private $session;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Security\AdministratorUserProvider
	 */
	private $administratorUserProvider;

	public function __construct(SessionInterface $session, AdministratorUserProvider $administratorUserProvider) {
		$this->session = $session;
		$this->administratorUserProvider = $administratorUserProvider;
	}

	/**
	 * @return bool
	 * @see \Symfony\Component\Security\Http\Firewall\ContextListener::handle()
	 */
	public function isAdministratorLogged() {
		$serializedToken = $this->session->get('_security_' . self::ADMINISTRATION_CONTEXT);
		if ($serializedToken === null) {
			return false;
		}

		$token = unserialize($serializedToken);
		if (!$token instanceof TokenInterface) {
			return false;
		}

		try {
			$this->refreshUserInToken($token);
		} catch (\SS6\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenUserException $e) {
			return false;
		}

		return $token->isAuthenticated();
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
			throw new \SS6\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenUserException($message);
		}

		try {
			$token->setUser($this->administratorUserProvider->refreshUser($user));
		} catch (\Symfony\Component\Security\Core\Exception\UnsupportedUserException $e) {
			$message = 'AdministratorUserProvider does not support user in this token.';
			throw new \SS6\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenUserException($message, $e);
		} catch (\Symfony\Component\Security\Core\Exception\UsernameNotFoundException $e) {
			$message = 'Username not found.';
			throw new \SS6\ShopBundle\Model\Administrator\Security\Exception\InvalidTokenUserException($message, $e);
		}
	}

}
