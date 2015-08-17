<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Login\LoginFormType;
use SS6\ShopBundle\Model\Security\LoginService;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller {

	/**
	 * @Route("/", name="admin_login")
	 * @Route("/login_check/", name="admin_login_check")
	 * @Route("/logout/", name="admin_logout")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function loginAction(Request $request) {
		if ($this->isGranted(Roles::ROLE_ADMIN)) {
			return $this->redirect($this->generateUrl('admin_default_dashboard'));
		}

		$error = null;

		$form = $this->createForm(new LoginFormType(), null, [
			'action' => $this->generateUrl('admin_login_check'),
			'method' => 'POST',
		]);

		$loginService = $this->container->get(LoginService::class);
		/* @var $loginService \SS6\ShopBundle\Model\Security\LoginService */
		try {
			$loginService->checkLoginProcess($request);
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$error = 'Přihlášení se nepodařilo.';
		}

		return $this->render('@SS6Shop/Admin/Content/Login/loginForm.html.twig', [
				'form' => $form->createView(),
				'error' => $error,
		]);
	}

}
