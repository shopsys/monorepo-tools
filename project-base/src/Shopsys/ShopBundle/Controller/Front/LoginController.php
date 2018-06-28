<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Model\Security\LoginService;
use Shopsys\FrameworkBundle\Model\Security\Roles;
use Shopsys\ShopBundle\Form\Front\Login\LoginFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Security\LoginService
     */
    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function loginAction(Request $request)
    {
        if ($this->isGranted(Roles::ROLE_LOGGED_CUSTOMER)) {
            return $this->redirectToRoute('front_homepage');
        }

        $form = $this->getLoginForm();

        try {
            $this->loginService->checkLoginProcess($request);
        } catch (\Shopsys\FrameworkBundle\Model\Security\Exception\LoginFailedException $e) {
            $form->addError(new FormError(t('Invalid login')));
        }

        return $this->render('@ShopsysShop/Front/Content/Login/loginForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function windowFormAction()
    {
        return $this->render('@ShopsysShop/Front/Content/Login/windowForm.html.twig', [
            'form' => $this->getLoginForm()->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function getLoginForm()
    {
        return $this->createForm(LoginFormType::class, null, [
            'action' => $this->generateUrl('front_login_check'),
        ]);
    }
}
