<?php

namespace Shopsys\ShopBundle\Model\Security;

use DateTime;
use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade;
use Shopsys\ShopBundle\Model\Administrator\Administrator;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Order\OrderFlowFacade;
use Shopsys\ShopBundle\Model\Security\TimelimitLoginInterface;
use Shopsys\ShopBundle\Model\Security\UniqueLoginInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Order\OrderFlowFacade
     */
    private $orderFlowFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Administrator\Activity\AdministratorActivityFacade
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
