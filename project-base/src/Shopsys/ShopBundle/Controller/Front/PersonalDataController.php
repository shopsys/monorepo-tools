<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Controller\FrontBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMailFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade;
use Shopsys\ShopBundle\Form\Front\PersonalData\PersonalDataFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalDataController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade
     */
    private $newsletterFacade;

    /*
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade
     */
    private $personalDataAccessRequestFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMailFacade
     */
    private $personalDataAccessMailFacade;

    public function __construct(
        Setting $setting,
        Domain $domain,
        CustomerFacade $customerFacade,
        OrderFacade $orderFacade,
        NewsletterFacade $newsletterFacade,
        PersonalDataAccessMailFacade $personalDataAccessMailFacade,
        PersonalDataAccessRequestFacade $personalDataAccessRequestFacade
    ) {
        $this->setting = $setting;
        $this->domain = $domain;
        $this->customerFacade = $customerFacade;
        $this->orderFacade = $orderFacade;
        $this->newsletterFacade = $newsletterFacade;
        $this->personalDataAccessMailFacade = $personalDataAccessMailFacade;
        $this->personalDataAccessRequestFacade = $personalDataAccessRequestFacade;
    }

    public function indexAction(Request $request)
    {
        $form = $this->createForm(PersonalDataFormType::class);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $personalData = $this->personalDataAccessRequestFacade->createPersonalDataAccessRequest(
                $form->getData(),
                $this->domain->getId()
            );
            $this->personalDataAccessMailFacade->sendMail($personalData);
            $this->getFlashMessageSender()->addSuccessFlash(t('Email with link to personal data access site was sent'));
        }

        return $this->render('@ShopsysShop/Front/Content/PersonalData/index.html.twig', [
            'personalDataSiteContent' => $this->getPersonalDataSiteContent(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $hash
     */
    public function accessAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findEmailByHashAndDomainId($hash, $this->domain->getId());

        if ($personalDataAccessRequest !== null) {
            $user = $this->customerFacade->findUserByEmailAndDomain(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );
            $orders = $this->orderFacade->getOrderListForEmailByDomainId(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );
            $newsletterSubscriber = $this->newsletterFacade->findNewsletterSubscriberByEmailAndDomainId(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );

            return $this->render('@ShopsysShop/Front/Content/PersonalData/detail.html.twig', [
                'personalDataAccessRequest' => $personalDataAccessRequest,
                'orders' => $orders,
                'user' => $user,
                'newsletterSubscriber' => $newsletterSubscriber,
            ]);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @return string|null
     */
    private function getPersonalDataSiteContent()
    {
        return $this->setting->getForDomain(Setting::PERSONAL_DATA_SITE_CONTENT, $this->domain->getId());
    }
}
