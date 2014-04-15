<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Form\Admin\Login\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller {

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function loginAction(Request $request) {
		if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
			return $this->redirect($this->generateUrl('admin_homepage'));
		}
		
		$error = null;
		
		$form = $this->createForm(new LoginFormType(), null, array(
			'action' =>	$this->get('router')->generate('admin_login_check', array(), true),
			'method' => 'POST',
		));

		$loginService = $this->container->get('ss6.core.security.login_service');
		/* @var $loginService SS6\ShopBundle\Model\Security\LoginService */
		try {
			$loginService->checkLoginProcess($request);
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$error = 'Přihlášení se nepodařilo.';
		}

		return $this->render('@SS6Shop/Admin/Content/Login/loginForm.html.twig', array(
				'form' => $form->createView(),
				'error' => $error,
		));
	}

}
