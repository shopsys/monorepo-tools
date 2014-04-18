<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Login\LoginFormType;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller {

	public function loginAction(Request $request) {
		if ($this->get('security.context')->isGranted(Roles::ROLE_CUSTOMER)) {
			return $this->redirect($this->generateUrl('front_homepage'));
		}
		
		$error = null;
		$loginService = $this->container->get('ss6.shop.security.login_service');
		/* @var $loginService \SS6\ShopBundle\Model\Security\LoginService */
		try {
			$loginService->checkLoginProcess($request);
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$error = 'Zadal jste špatné přihlašovací údaje.';
		}

		return $this->render('@SS6Shop/Front/Content/Login/loginForm.html.twig', array(
			'form' => $this->getLoginForm()->createView(),
			'error' => $error,
		));
	}
	
	/**
	 * @param string $windowName
	 */
	public function windowInitAction($windowName) {
		return $this->render('@SS6Shop/Front/Inline/Login/windowInit.html.twig', array(
			'form' => $this->getLoginForm()->createView(),
			'windowName' => $windowName,
		));
	}
	
	/**
	 * @return \Symfony\Component\Form\Form
	 */
	private function getLoginForm() {
		return $this->createForm(new LoginFormType(), null, array(
			'action' =>	$this->get('router')->generate('front_login_check', array(), true),
			'method' => 'POST',
		));
	}

}
