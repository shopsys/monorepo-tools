<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Front\Customer\Password\ResetPasswordFormType;
use Shopsys\ShopBundle\Model\Customer\CustomerPasswordFacade;
use Shopsys\ShopBundle\Model\Security\LoginService;
use Symfony\Component\HttpFoundation\Request;

class CustomerPasswordController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerPasswordFacade
     */
    private $customerPasswordFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Model\Security\LoginService
     */
    private $loginService;

    public function __construct(
        Domain $domain,
        CustomerPasswordFacade $customerPasswordFacade,
        LoginService $loginService
    ) {
        $this->domain = $domain;
        $this->customerPasswordFacade = $customerPasswordFacade;
        $this->loginService = $loginService;
    }

    public function resetPasswordAction(Request $request)
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();
            $email = $formData['email'];

            try {
                $this->customerPasswordFacade->resetPassword($email, $this->domain->getId());

                $this->getFlashMessageSender()->addSuccessFlashTwig(
                    t('Link to password reset sent to e-mail <strong>{{ email }}</strong>.'),
                    [
                        'email' => $email,
                    ]
                );
                return $this->redirectToRoute('front_registration_reset_password');
            } catch (\Shopsys\ShopBundle\Model\Customer\Exception\UserNotFoundByEmailAndDomainException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('Customer with e-mail address <strong>{{ email }}</strong> doesn\'t exist. '
                        . '<a href="{{ registrationLink }}"> Register</a>'),
                    [
                        'email' => $ex->getEmail(),
                        'registrationLink' => $this->generateUrl('front_registration_register'),
                    ]
                );
            }
        }

        return $this->render('@ShopsysShop/Front/Content/Registration/resetPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function setNewPasswordAction(Request $request)
    {
        $email = $request->query->get('email');
        $hash = $request->query->get('hash');

        if (!$this->customerPasswordFacade->isResetPasswordHashValid($email, $this->domain->getId(), $hash)) {
            $this->getFlashMessageSender()->addErrorFlash(t('The link to change your password expired.'));
            return $this->redirectToRoute('front_homepage');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $formData = $form->getData();

            $newPassword = $formData['newPassword'];

            try {
                $user = $this->customerPasswordFacade->setNewPassword($email, $this->domain->getId(), $hash, $newPassword);

                $this->loginService->loginUser($user, $request);
            } catch (\Shopsys\ShopBundle\Model\Customer\Exception\UserNotFoundByEmailAndDomainException $ex) {
                $this->getFlashMessageSender()->addErrorFlashTwig(
                    t('Customer with e-mail address <strong>{{ email }}</strong> doesn\'t exist. '
                        . '<a href="{{ registrationLink }}"> Register</a>'),
                    [
                        'email' => $ex->getEmail(),
                        'registrationLink' => $this->generateUrl('front_registration_register'),
                    ]
                );
            } catch (\Shopsys\ShopBundle\Model\Customer\Exception\InvalidResetPasswordHashException $ex) {
                $this->getFlashMessageSender()->addErrorFlash(t('The link to change your password expired.'));
            }

            $this->getFlashMessageSender()->addSuccessFlash(t('Password successfully changed'));
            return $this->redirectToRoute('front_homepage');
        }

        return $this->render('@ShopsysShop/Front/Content/Registration/setNewPassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
