<?php

namespace SS6\ShopBundle\Model\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class AdminLogoutHandler implements LogoutSuccessHandlerInterface {

	/**
	 * @var \Symfony\Component\Routing\Router
	 */
	private $router;

	/**
	 * @param \Symfony\Component\Routing\Router $router
	 */
	public function __construct(Router $router) {
		$this->router = $router;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function onLogoutSuccess(Request $request) {
		$url = $this->router->generate('admin_login');
		$request->getSession()->migrate();

		return new RedirectResponse($url);
	}

}
