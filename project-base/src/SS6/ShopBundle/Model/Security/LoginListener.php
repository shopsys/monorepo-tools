<?php

namespace SS6\ShopBundle\Model\Security;

use DateTime;
use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use SS6\ShopBundle\Model\Administrator\Administrator;
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
	 * @var \SS6\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade
	 */
	private $administratorActivityFacade;

	public function __construct(
		EntityManager $em,
		OrderFlowFacade $orderFlowFacade,
		AdministratorActivityFacade $administratorActivityFacade
	) {
		$this->em = $em;
		$this->orderFlowFacade = $orderFlowFacade;
		$this->administratorActivityFacade = $administratorActivityFacade;
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

		if ($user instanceof UniqueLoginInterface && !$user->isMultidomainLogin()) {
			$user->setLoginToken(uniqid());
		}

		if ($user instanceof Administrator) {
			$this->administratorActivityFacade->create(
				$user,
				$event->getRequest()->getClientIp()
			);
		}

		$this->em->flush();
	}
}
