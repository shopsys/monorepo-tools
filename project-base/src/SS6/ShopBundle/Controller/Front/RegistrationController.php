<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Form\Front\Registration\RegistrationFormType;
use SS6\ShopBundle\Model\Customer\CustomerFacade;
use SS6\ShopBundle\Model\Customer\UserDataFactory;
use SS6\ShopBundle\Model\Security\LoginService;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerFacade
	 */
	private $customerFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\UserDataFactory
	 */
	private $userDataFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Security\LoginService
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

	public function registerAction(Request $request) {
		$form = $this->createForm(new RegistrationFormType());

		$userData = $this->userDataFactory->createDefault($this->domain->getId());

		$form->setData($userData);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$userData = $form->getData();
			$userData->domainId = $this->domain->getId();

			try {
				$user = $this->customerFacade->register($userData);

				$this->loginService->loginUser($user, $request);

				$this->getFlashMessageSender()->addSuccessFlash(t('Byli jste úspěšně zaregistrováni'));

				return $this->redirectToRoute('front_homepage');
			} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
				$form->get('email')->addError(new FormError(t('V databázi se již nachází zákazník s tímto e-mailem')));
			}
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		return $this->render('@SS6Shop/Front/Content/Registration/register.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
