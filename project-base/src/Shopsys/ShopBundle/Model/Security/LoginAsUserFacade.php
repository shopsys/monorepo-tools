<?php

namespace SS6\ShopBundle\Model\Security;

use SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserRepository;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginAsUserFacade {

	const SESSION_LOGIN_AS = 'loginAsUser';

	/**
	 * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	private $eventDispatcher;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
	 */
	private $session;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserRepository
	 */
	private $userRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade
	 */
	private $administratorFrontSecurityFacade;

	/**
	 * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $tokenStorage
	 * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
	 * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
	 * @param \SS6\ShopBundle\Model\Customer\UserRepository $userRepository
	 * @param \SS6\ShopBundle\Model\Administrator\Security\AdministratorFrontSecurityFacade $administratorFrontSecurityFacade
	 */
	public function __construct(
		TokenStorageInterface $tokenStorage,
		EventDispatcherInterface $eventDispatcher,
		SessionInterface $session,
		UserRepository $userRepository,
		AdministratorFrontSecurityFacade $administratorFrontSecurityFacade
	) {
		$this->tokenStorage = $tokenStorage;
		$this->eventDispatcher = $eventDispatcher;
		$this->session = $session;
		$this->userRepository = $userRepository;
		$this->administratorFrontSecurityFacade = $administratorFrontSecurityFacade;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	public function rememberLoginAsUser(User $user) {
		$this->session->set(self::SESSION_LOGIN_AS, serialize($user));
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function loginAsRememberedUser(Request $request) {
		if (!$this->administratorFrontSecurityFacade->isAdministratorLogged()) {
			throw new \SS6\ShopBundle\Model\Security\Exception\LoginAsRememberedUserException('Access denied');
		}

		if (!$this->session->has(self::SESSION_LOGIN_AS)) {
			throw new \SS6\ShopBundle\Model\Security\Exception\LoginAsRememberedUserException('User not set.');
		}

		$unserializedUser = unserialize($this->session->get(self::SESSION_LOGIN_AS));
		/* @var $unserializedUser \SS6\ShopBundle\Model\Customer\User */
		$this->session->remove(self::SESSION_LOGIN_AS);
		$freshUser = $this->userRepository->getUserById($unserializedUser->getId());

		$password = '';
		$firewallName = 'frontend';
		$freshUserRoles = array_merge($freshUser->getRoles(), [Roles::ROLE_ADMIN_AS_CUSTOMER]);
		$token = new UsernamePasswordToken($freshUser, $password, $firewallName, $freshUserRoles);
		$this->tokenStorage->setToken($token);

		$event = new InteractiveLoginEvent($request, $token);
		$this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
	}

}
