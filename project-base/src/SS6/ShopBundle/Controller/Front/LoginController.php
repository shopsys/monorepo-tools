<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;
use SS6\ShopBundle\Form\Front\Login\LoginFormType;
use SS6\ShopBundle\Model\Security\LoginService;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController {

	public function loginAction(Request $request) {
		if ($this->isGranted(Roles::ROLE_CUSTOMER)) {
			return $this->redirect($this->generateUrl('front_homepage'));
		}

		$loginService = $this->container->get(LoginService::class);
		/* @var $loginService \SS6\ShopBundle\Model\Security\LoginService */

		$form = $this->getLoginForm();

		try {
			$loginService->checkLoginProcess($request);
		} catch (\SS6\ShopBundle\Model\Security\Exception\LoginFailedException $e) {
			$form->addError(new FormError('Byly zadány neplatné přihlašovací údaje'));
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
