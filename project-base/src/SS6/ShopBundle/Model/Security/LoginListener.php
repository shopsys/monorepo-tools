<?php

namespace SS6\ShopBundle\Model\Security;

use DateTime;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener {
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em) {
		$this->em = $em;
	}
	
	/**
	 * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
	 */
	public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
		$token = $event->getAuthenticationToken();
		$user = $token->getUser();
		
		if ($user instanceof TimelimitLoginInterface) {
			$user->setLastActivity(new DateTime());
		}

		if ($user instanceof User) {
			$user->setLastLogin(new DateTime());
			$this->em->persist($user);
		}

		if ($user instanceof UniqueLoginInterface) {
			$user->setLoginToken(uniqid());
			$this->em->persist($user);
		}

		$this->em->flush();
	}
}
