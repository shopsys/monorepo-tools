<?php

namespace Shopsys\FrameworkBundle\Model\Security;

use Shopsys\FrameworkBundle\Model\Order\OrderFlowFacade;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class FrontLogoutHandler implements LogoutSuccessHandlerInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFlowFacade
     */
    private $orderFlowFacade;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    private $router;

    /**
     * @param \Symfony\Component\Routing\Router $router
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderFlowFacade $orderFlowFacade
     */
    public function __construct(Router $router, OrderFlowFacade $orderFlowFacade)
    {
        $this->router = $router;
        $this->orderFlowFacade = $orderFlowFacade;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $this->orderFlowFacade->resetOrderForm();
        $url = $this->router->generate('front_homepage');
        $request->getSession()->migrate();

        return new RedirectResponse($url);
    }
}
