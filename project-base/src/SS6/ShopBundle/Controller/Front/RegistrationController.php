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

				$this->getFlashMessageSender()->addSuccessFlash(t('You have been successfully registered.'));

				return $this->redirectToRoute('front_homepage');
			} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
				$form->get('email')->addError(new FormError(t('There is already a customer with this e-mail in the database')));
			}
		}

		if ($form->isSubmitted() && !$form->isValid()) {
			$this->getFlashMessageSender()->addErrorFlash(t('Please check the correctness of all data filled.'));
		}

		return $this->render('@SS6Shop/Front/Content/Registration/register.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
