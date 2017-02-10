<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Form\Front\Contact\ContactFormType;
use Shopsys\ShopBundle\Model\ContactForm\ContactFormData;
use Shopsys\ShopBundle\Model\ContactForm\ContactFormFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\ContactForm\ContactFormFacade
     */
    private $contactFormFacade;

    public function __construct(ContactFormFacade $contactFormFacade) {
        $this->contactFormFacade = $contactFormFacade;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function sendAction(Request $request) {
        $form = $this->createForm(
            new ContactFormType(),
            new ContactFormData(),
            [
                'action' => $this->generateUrl('front_contact_form_send'),
                'method' => 'POST',
            ]
        );
        $form->handleRequest($request);

        $message = '';
        if ($form->isValid()) {
            $contactFormData = $form->getData();

            try {
                $this->contactFormFacade->sendMail($contactFormData);
                $form = $this->createForm(
                    new ContactFormType(),
                    new ContactFormData(),
                    [
                        'action' => $this->generateUrl('front_contact_form_send'),
                        'method' => 'POST',
                    ]
                );
                $message = t('Thank you, your message has been sent.');
            } catch (\Shopsys\ShopBundle\Model\Mail\Exception\SendMailFailedException $ex) {
                $message = t('Error occurred when sending e-mail.');
            }

        }

        $contactFormHtml = $this->renderView('@ShopsysShop/Front/Content/ContactForm/contactForm.html.twig', [
            'form' => $form->createView(),
        ]);

        return new JsonResponse([
            'contactFormHtml' => $contactFormHtml,
            'message' => $message,
        ]);

    }

    public function indexAction() {
        $form = $this->createForm(
            new ContactFormType(),
            new ContactFormData(),
            [
                'action' => $this->generateUrl('front_contact_form_send'),
                'method' => 'POST',
            ]
        );

        return $this->render('@ShopsysShop/Front/Content/ContactForm/contactForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
