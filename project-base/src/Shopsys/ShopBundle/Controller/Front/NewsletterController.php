<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Form\Front\Newsletter\SubscriptionFormType;
use Shopsys\ShopBundle\Model\LegalConditions\LegalConditionsFacade;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade
     */
    private $newsletterFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\LegalConditions\LegalConditionsFacade
     */
    private $legalConditionsFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    public function __construct(
        NewsletterFacade $newsletterFacade,
        LegalConditionsFacade $legalConditionsFacade,
        Domain $domain
    ) {
        $this->newsletterFacade = $newsletterFacade;
        $this->legalConditionsFacade = $legalConditionsFacade;
        $this->domain = $domain;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response|NULL
     */
    public function subscribeEmailAction(Request $request)
    {
        $form = $this->createSubscriptionForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $this->newsletterFacade->addSubscribedEmail($email);
        }

        return $this->renderSubscription($form);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function subscriptionAction(): Response
    {
        $form = $this->createSubscriptionForm();

        return $this->renderSubscription($form);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createSubscriptionForm(): Form
    {
        return $this->createForm(SubscriptionFormType::class, null, [
            'action' => $this->generateUrl('front_newsletter_send'),
        ]);
    }

    /**
     * @param \Symfony\Component\Form\Form $form
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function renderSubscription(Form $form): Response
    {
        $privacyPolicyArticle = $this->legalConditionsFacade->findPrivacyPolicy($this->domain->getId());

        return $this->render('@ShopsysShop/Front/Inline/Newsletter/subscription.html.twig', [
            'form' => $form->createView(),
            'success' => $form->isSubmitted() && $form->isValid(),
            'privacyPolicyArticle' => $privacyPolicyArticle,
        ]);
    }
}
