<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Form\Admin\Login\LoginFormType;
use SS6\ShopBundle\Model\Security\LoginService;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Security\LoginService
	 */
	private $loginService;

	public function __construct(LoginService $loginService) {
		$this->loginService = $loginService;
	}

	/**
	 * @Route("/", name="admin_login")
	 * @Route("/login_check/", name="admin_login_check")
	 * @Route("/logout/", name="admin_logout")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function loginAction(Request $request) {
		if ($this->isGranted(Roles::ROLE_ADMIN)) {
			return $this->redirectToRoute('admin_default_dashboard');
		}

		$error = null;

		$form = $this->createForm(new LoginFormType(), null, [
			'action' => $this->generateUrl('admin_login_check'),
			'method' => 'POST',
		]);

		try {
			$this->loginService->checkLoginProcess($request);
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$error = 'Přihlášení se nepodařilo.';
		}

		return $this->render('@SS6Shop/Admin/Content/Login/loginForm.html.twig', [
				'form' => $form->createView(),
				'error' => $error,
		]);
	}

}
