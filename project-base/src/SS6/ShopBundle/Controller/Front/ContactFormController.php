<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;
use SS6\ShopBundle\Form\Front\Contact\ContactFormType;
use SS6\ShopBundle\Model\ContactForm\ContactFormData;
use SS6\ShopBundle\Model\ContactForm\ContactFormFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\ContactForm\ContactFormFacade
	 */
	private $contactFormFacade;

	public function __construct(ContactFormFacade $contactFormFacade) {
		$this->contactFormFacade = $contactFormFacade;
	}

	/**
	 * @param Request $request
	 */
	public function indexAction(Request $request) {
		$flashMessageBag = $this->get('ss6.shop.flash_message.bag.front');
		$form = $this->createForm(
			new ContactFormType(),
			new ContactFormData(), [
				'action' => $this->generateUrl('front_contact_form'),
				'method' => 'POST',
		]);
		$form->handleRequest($request);

		if ($form->isValid()) {
			$contactFormData = $form->getData();

			try {
				$this->contactFormFacade->sendMail($contactFormData);
				$form = $this->createForm(
					new ContactFormType(),
					new ContactFormData(), [
						'action' => $this->generateUrl('front_contact_form'),
						'method' => 'POST',
				]);
				$this->getFlashMessageSender()->addSuccessFlash('Děkujeme, váš vzkaz byl odeslán.');
			} catch (\SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException $ex) {
				$this->getFlashMessageSender()->addErrorFlash('Nastala chyba při odesílání mailu.');
			}

			if ($request->isXmlHttpRequest()) {
				$contactFormHtml = $this->renderView('@SS6Shop/Front/Content/Contact/contactForm.html.twig', [
					'form' => $form->createView(),
				]);

				return new JsonResponse([
					'contactFormHtml' => $contactFormHtml,
					'successMessages' => $flashMessageBag->getSuccessMessages(),
					'errorMessages' => $flashMessageBag->getErrorMessages(),
				]);
			}
		}

		return $this->render('@SS6Shop/Front/Content/Contact/contactForm.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
