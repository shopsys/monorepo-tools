<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Front\Registration\RegistrationFormType;
use Shopsys\ShopBundle\Model\Customer\CustomerFacade;
use Shopsys\ShopBundle\Model\Customer\UserDataFactory;
use Shopsys\ShopBundle\Model\Security\LoginService;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Customer\UserDataFactory
     */
    private $userDataFactory;

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
        UserDataFactory $userDataFactory,
        CustomerFacade $customerFacade,
        LoginService $loginService
    ) {
        $this->domain = $domain;
        $this->userDataFactory = $userDataFactory;
        $this->customerFacade = $customerFacade;
        $this->loginService = $loginService;
    }

    public function registerAction(Request $request)
    {
        $userData = $this->userDataFactory->createDefault($this->domain->getId());

        $form = $this->createForm(RegistrationFormType::class, $userData);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $userData = $form->getData();
            $userData->domainId = $this->domain->getId();

            $user = $this->customerFacade->register($userData);
            $this->loginService->loginUser($user, $request);

            $this->getFlashMessageSender()->addSuccessFlash(t('You have been successfully registered.'));
            return $this->redirectToRoute('front_homepage');
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
        }

        return $this->render('@ShopsysShop/Front/Content/Registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
