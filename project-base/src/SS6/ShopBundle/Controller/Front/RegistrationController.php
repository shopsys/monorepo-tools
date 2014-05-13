<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Registration\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller {

	public function registerAction(Request $request) {
		$flashMessage = $this->get('ss6.shop.flash_message.front');
		/* @var $flashMessage \SS6\ShopBundle\Model\FlashMessage\FlashMessage */

		$form = $this->createForm(new RegistrationFormType());

		try {
			$userData = array();

			$form->setData($userData);
			$form->handleRequest($request);

			if ($form->isValid()) {
				$registrationFacade = $this->get('ss6.shop.customer.registration_facade');
				/* @var $registrationFacade \SS6\ShopBundle\Model\Customer\RegistrationFacade */

				$userData = $form->getData();
				$registrationFacade->register(
					$userData['firstName'],
					$userData['lastName'],
					$userData['email'],
					$userData['password']);

				$flashMessage->addSuccess('Byli jste úspěšně zaregistrováni');
				return $this->redirect($this->generateUrl('front_homepage'));
			} elseif ($form->isSubmitted()) {
				$flashMessage->addError('Prosím zkontrolujte si správnost vyplnění všech údajů');
			}
		} catch (\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException $e) {
			$form->get('email')->addError(new FormError('Uživatel s tímto emailem již existuje'));
		}

		return $this->render('@SS6Shop/Front/Content/Registration/register.html.twig', array(
			'form' => $form->createView(),
		));
	}

}
