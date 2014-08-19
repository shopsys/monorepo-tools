<?php

namespace SS6\ShopBundle\Model\Security;

use DateTime;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Order\OrderFlowFacade;
use SS6\ShopBundle\Model\Security\TimelimitLoginInterface;
use SS6\ShopBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Order\OrderFlowFacade
	 */
	private $orderFlowFacade;

	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Order\OrderFlowFacade $orderFlowFacade
	 */
	public function __construct(EntityManager $em, OrderFlowFacade $orderFlowFacade) {
		$this->em = $em;
		$this->orderFlowFacade = $orderFlowFacade;
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
			$user->onLogin();
			$this->orderFlowFacade->resetOrderForm();
		}

		if ($user instanceof UniqueLoginInterface) {
			$user->setLoginToken(uniqid());
		}

		$this->em->flush();
	}
}
