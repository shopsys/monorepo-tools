<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Form\Front\Login\LoginFormType;
use SS6\ShopBundle\Model\Security\LoginService;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Security\LoginService
	 */
	private $loginService;

	public function __construct(LoginService $loginService) {
		$this->loginService = $loginService;
	}

	public function loginAction(Request $request) {
		if ($this->isGranted(Roles::ROLE_CUSTOMER)) {
			return $this->redirectToRoute('front_homepage');
		}

		$form = $this->getLoginForm();

		try {
			$this->loginService->checkLoginProcess($request);
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$form->addError(new FormError(t('Invalid login')));
		}

		return $this->render('@SS6Shop/Front/Content/Login/loginForm.html.twig', [
			'form' => $form->createView(),
		]);
	}

	public function windowFormAction() {
		return $this->render('@SS6Shop/Front/Content/Login/windowForm.html.twig', [
			'form' => $this->getLoginForm()->createView(),
		]);
	}

	/**
	 * @return \Symfony\Component\Form\Form
	 */
	private function getLoginForm() {
		return $this->createForm(new LoginFormType(), null, [
			'action' => $this->generateUrl('front_login_check'),
			'method' => 'POST',
		]);
	}

}
