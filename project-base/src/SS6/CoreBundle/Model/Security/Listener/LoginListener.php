<?php

namespace SS6\CoreBundle\Model\Security\Listener;

use Doctrine\ORM\EntityManager;
use SS6\CoreBundle\Model\Security\SingletonLoginInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener {
	
	/**
	 * @var Session
	 */
	private $session;
	
	/**
	 * @var EntityManager
	 */
	private $em;
	
	/**
	 * @param \Symfony\Component\HttpFoundation\Session\Session $session
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(Session $session, EntityManager $em) {
		$this->session = $session;
		$this->em = $em;
	}
	
	/**
	 * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
		$token = $event->getAuthenticationToken();
		$user = $token->getUser();
		
		if ($user instanceof SingletonLoginInterface) {
			$user->setLoginToken(uniqid());
			$this->em->persist($user);
			$this->em->flush();
		}
		
	}
}
