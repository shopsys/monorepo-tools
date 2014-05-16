<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Registration\RegistrationFormType;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

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
				$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
				/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */

				$userData = $form->getData();
				$user = $customerEditFacade->register(
					$userData['firstName'],
					$userData['lastName'],
					$userData['email'],
					$userData['password']);

				$this->login($user);

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

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 */
	private function login(User $user) {
		$token = new UsernamePasswordToken($user, $user->getPassword(), 'frontend', $user->getRoles());
		$this->get('security.context')->setToken($token);

		// dispatch the login event
		$request = $this->get('request');
		$event = new InteractiveLoginEvent($request, $token);
		$this->get('event_dispatcher')->dispatch('security.interactive_login', $event);
	}

}
