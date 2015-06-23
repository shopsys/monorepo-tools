<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Controller\Front\BaseController;
use SS6\ShopBundle\Form\Front\Contact\ContactFormType;
use SS6\ShopBundle\Model\ContactForm\ContactFormData;
use SS6\ShopBundle\Model\ContactForm\ContactFormFacade;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\ContactForm\ContactFormFacade
	 */
	private $contactFormFacade;

	public function __construct(ContactFormFacade $contactFormFacade) {
		$this->contactFormFacade = $contactFormFacade;
	}

	public function indexAction(Request $request) {
		$form = $this->createForm(new ContactFormType(), new ContactFormData());
		$form->handleRequest($request);

		if ($form->isValid()) {
			$contactFormData = $form->getData();

			try {
				$this->contactFormFacade->sendMail($contactFormData);
				$this->getFlashMessageSender()->addSuccessFlash('Děkujeme, váš vzkaz byl odeslán.');
				$form = $this->createForm(new ContactFormType(), new ContactFormData());
			} catch (\SS6\ShopBundle\Model\Mail\Exception\SendMailFailedException $ex) {
				$this->getFlashMessageSender()->addErrorFlash('Nastala chyba při odesílání mailu.');
			}
		}

		return $this->render('@SS6Shop/Front/Content/Contact/contactForm.html.twig', [
			'form' => $form->createView(),
		]);
	}

}
