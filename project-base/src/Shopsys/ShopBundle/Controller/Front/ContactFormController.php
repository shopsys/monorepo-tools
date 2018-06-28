<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\ContactForm\ContactFormData;
use Shopsys\FrameworkBundle\Model\ContactForm\ContactFormFacade;
use Shopsys\FrameworkBundle\Model\LegalConditions\LegalConditionsFacade;
use Shopsys\ShopBundle\Form\Front\Contact\ContactFormType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\ContactForm\ContactFormFacade
     */
    private $contactFormFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\LegalConditions\LegalConditionsFacade
     */
    private $legalConditionsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        ContactFormFacade $contactFormFacade,
        LegalConditionsFacade $legalConditionsFacade,
        Domain $domain
    ) {
        $this->contactFormFacade = $contactFormFacade;
        $this->legalConditionsFacade = $legalConditionsFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function sendAction(Request $request)
    {
        $privacyPolicyArticle = $this->legalConditionsFacade->findPrivacyPolicy($this->domain->getId());

        $form = $this->createForm(ContactFormType::class, new ContactFormData(), [
            'action' => $this->generateUrl('front_contact_form_send'),
        ]);
        $form->handleRequest($request);

        $message = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();

            try {
                $this->contactFormFacade->sendMail($contactFormData);
                $form = $this->createForm(ContactFormType::class, new ContactFormData(), [
                    'action' => $this->generateUrl('front_contact_form_send'),
                ]);
                $message = t('Thank you, your message has been sent.');
            } catch (\Shopsys\FrameworkBundle\Model\Mail\Exception\MailException $ex) {
                $message = t('Error occurred when sending e-mail.');
            }
        }

        $contactFormHtml = $this->renderView('@ShopsysShop/Front/Content/ContactForm/contactForm.html.twig', [
            'form' => $form->createView(),
            'privacyPolicyArticle' => $privacyPolicyArticle,
        ]);

        return new JsonResponse([
            'contactFormHtml' => $contactFormHtml,
            'message' => $message,
        ]);
    }

    public function indexAction()
    {
        $privacyPolicyArticle = $this->legalConditionsFacade->findPrivacyPolicy($this->domain->getId());

        $form = $this->createForm(ContactFormType::class, new ContactFormData(), [
            'action' => $this->generateUrl('front_contact_form_send'),
        ]);

        return $this->render('@ShopsysShop/Front/Content/ContactForm/contactForm.html.twig', [
            'form' => $form->createView(),
            'privacyPolicyArticle' => $privacyPolicyArticle,
        ]);
    }
}
