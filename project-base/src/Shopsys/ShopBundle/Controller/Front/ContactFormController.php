<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Front\Contact\ContactFormType;
use Shopsys\ShopBundle\Model\ContactForm\ContactFormData;
use Shopsys\ShopBundle\Model\ContactForm\ContactFormFacade;
use Shopsys\ShopBundle\Model\LegalConditions\LegalConditionsFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContactFormController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\ContactForm\ContactFormFacade
     */
    private $contactFormFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\LegalConditions\LegalConditionsFacade
     */
    private $legalConditionsFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
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
            } catch (\Shopsys\ShopBundle\Model\Mail\Exception\MailException $ex) {
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
