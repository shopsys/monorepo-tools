<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Controller\FrontBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Form\Front\PersonalData\PersonalDataFormType;
use Symfony\Component\HttpFoundation\Request;

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

    public function __construct(
        Setting $setting,
        Domain $domain,
        CustomerFacade $customerFacade,
        OrderFacade $orderFacade,
        NewsletterFacade $newsletterFacade
    ) {
        $this->setting = $setting;
        $this->domain = $domain;
        $this->customerFacade = $customerFacade;
        $this->orderFacade = $orderFacade;
        $this->newsletterFacade = $newsletterFacade;
    }

    public function indexAction(Request $request)
    {
        $form = $this->createForm(PersonalDataFormType::class);
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            return $this->redirectToRoute('front_personal_data_access', ['email' => $form->getData()['email']]);
        }
        return $this->render('@ShopsysShop/Front/Content/PersonalData/index.html.twig', [
            'personalDataSiteContent' => $this->getPersonalDataSiteContent(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $email
     */
    public function accessAction($email)
    {
        $form = $this->createForm(PersonalDataFormType::class);
        $user = $this->customerFacade->findUserByEmailAndDomain($email, $this->domain->getId());
        $orders = $this->orderFacade->getOrderListForEmailByDomainId($email, $this->domain->getId());
        $newsletterSubscriber = $this->newsletterFacade->findNewsletterSubscriberByEmailAndDomainId(
            $email,
            $this->domain->getId()
        );
        return $this->render('@ShopsysShop/Front/Content/PersonalData/detail.html.twig', [
                'personalDataSiteContent' => $this->getPersonalDataSiteContent(),
                'form' => $form->createView(),
                'orders' => $orders,
                'user' => $user,
                'newsletterSubscriber' => $newsletterSubscriber,
            ]);
    }

    /**
     * @return string|null
     */
    private function getPersonalDataSiteContent()
    {
        return $this->setting->getForDomain(Setting::PERSONAL_DATA_SITE_CONTENT, $this->domain->getId());
    }
}
