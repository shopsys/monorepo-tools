<?php

namespace SS6\ShopBundle\Model\Security;

use DateTime;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\String\HashGenerator;
use SS6\ShopBundle\Model\Administrator\Administrator;
use SS6\ShopBundle\Model\Administrator\AdministratorRepository;
use SS6\ShopBundle\Model\Administrator\Security\AdministratorSecurityFacade;
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

class AdministratorLoginFacade {

	const MULTIDOMAIN_LOGIN_TOKEN_LENGTH = 50;
	const MULTIDOMAIN_LOGIN_TOKEN_VALID_SECONDS = 10;

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
	 * @var \SS6\ShopBundle\Model\Administrator\Security\AdministratorSecurityFacade
	 */
	private $administratorSecurityFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Administrator\AdministratorRepository
	 */
	private $administratorRepository;

	/**
	 * @var \SS6\ShopBundle\Component\String\HashGenerator
	 */
	private $hashGenerator;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	public function __construct(
		TokenStorageInterface $tokenStorage,
		EventDispatcherInterface $eventDispatcher,
		SessionInterface $session,
		UserRepository $userRepository,
		AdministratorSecurityFacade $administratorSecurityFacade,
		AdministratorRepository $administratorRepository,
		HashGenerator $hashGenerator,
		EntityManager $em
	) {
		$this->tokenStorage = $tokenStorage;
		$this->eventDispatcher = $eventDispatcher;
		$this->session = $session;
		$this->userRepository = $userRepository;
		$this->administratorSecurityFacade = $administratorSecurityFacade;
		$this->administratorRepository = $administratorRepository;
		$this->hashGenerator = $hashGenerator;
		$this->em = $em;
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
		if (!$this->administratorSecurityFacade->isAdministratorLogged()) {
			throw new \SS6\ShopBundle\Model\Security\Exception\LoginAsRememberedUserException('Access denied');
		}

		if (!$this->session->has(self::SESSION_LOGIN_AS)) {
			throw new \SS6\ShopBundle\Model\Security\Exception\LoginAsRememberedUserException('User not set.');
		}

		$unserializedUser = unserialize($this->session->get(self::SESSION_LOGIN_AS));
		/* @var $unserializedUser \SS6\ShopBundle\Model\Customer\User */
		$this->session->remove(self::SESSION_LOGIN_AS);
		$freshUser = $this->userRepository->getUserById($unserializedUser->getId());

		if ($unserializedUser->getPassword() !== $freshUser->getPassword()) {
			throw new \SS6\ShopBundle\Model\Security\Exception\LoginAsRememberedUserException('The credentials were changed.');
		}

		$password = '';
		$firewallName = 'frontend';
		$freshUserRoles = array_merge($freshUser->getRoles(), [Roles::ROLE_ADMIN_AS_CUSTOMER]);
		$token = new UsernamePasswordToken($freshUser, $password, $firewallName, $freshUserRoles);
		$this->tokenStorage->setToken($token);

		$event = new InteractiveLoginEvent($request, $token);
		$this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Administrator\Administrator $administrator
	 * @return string
	 */
	public function generateMultidomainLoginTokenWithExpiration(Administrator $administrator) {
		$multidomainLoginToken = $this->hashGenerator->generateHash(self::MULTIDOMAIN_LOGIN_TOKEN_LENGTH);
		$multidomainLoginTokenExpirationDateTime = new DateTime('+' . self::MULTIDOMAIN_LOGIN_TOKEN_VALID_SECONDS . 'seconds');
		$administrator->setMultidomainLoginTokenWithExpiration($multidomainLoginToken, $multidomainLoginTokenExpirationDateTime);
		$this->em->flush();

		return $multidomainLoginToken;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param string $multidomainLoginToken
	 */
	public function loginByMultidomainToken(Request $request, $multidomainLoginToken) {
		$administrator = $this->administratorRepository->getByValidMultidomainLoginToken($multidomainLoginToken);
		$administrator->setMultidomainLogin(true);
		$password = '';
		$firewallName = 'administration';
		$token = new UsernamePasswordToken($administrator, $password, $firewallName, $administrator->getRoles());
		$this->tokenStorage->setToken($token);

		$event = new InteractiveLoginEvent($request, $token);
		$this->eventDispatcher->dispatch(SecurityEvents::INTERACTIVE_LOGIN, $event);
	}

	public function invalidateCurrentAdministratorLoginToken() {
		$token = $this->tokenStorage->getToken();
		if ($token !== null) {
			$currentAdministrator = $token->getUser();
			$currentAdministrator->setLoginToken('');

			$this->em->flush($currentAdministrator);
		}
	}

}
