<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\HttpFoundation\XmlResponse;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMailFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestDataFactoryInterface;
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

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestDataFactoryInterface
     */
    private $personalDataAccessRequestDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\HttpFoundation\XmlResponse
     */
    private $xmlResponse;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade $customerFacade
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade
     * @param \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade $newsletterFacade
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMailFacade $personalDataAccessMailFacade
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade $personalDataAccessRequestFacade
     * @param \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestDataFactoryInterface $personalDataAccessRequestDataFactory
     * @param \Shopsys\FrameworkBundle\Component\HttpFoundation\XmlResponse $xmlResponse
     */
    public function __construct(
        Setting $setting,
        Domain $domain,
        CustomerFacade $customerFacade,
        OrderFacade $orderFacade,
        NewsletterFacade $newsletterFacade,
        PersonalDataAccessMailFacade $personalDataAccessMailFacade,
        PersonalDataAccessRequestFacade $personalDataAccessRequestFacade,
        PersonalDataAccessRequestDataFactoryInterface $personalDataAccessRequestDataFactory,
        XmlResponse $xmlResponse
    ) {
        $this->setting = $setting;
        $this->domain = $domain;
        $this->customerFacade = $customerFacade;
        $this->orderFacade = $orderFacade;
        $this->newsletterFacade = $newsletterFacade;
        $this->personalDataAccessMailFacade = $personalDataAccessMailFacade;
        $this->personalDataAccessRequestFacade = $personalDataAccessRequestFacade;
        $this->personalDataAccessRequestDataFactory = $personalDataAccessRequestDataFactory;
        $this->xmlResponse = $xmlResponse;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(
            PersonalDataFormType::class,
            $this->personalDataAccessRequestDataFactory->createForDisplay()
        );

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $personalData = $this->personalDataAccessRequestFacade->createPersonalDataAccessRequest(
                $form->getData(),
                $this->domain->getId()
            );
            $this->personalDataAccessMailFacade->sendMail($personalData);
            $this->getFlashMessageSender()->addSuccessFlash(t('E-mail with a link to the page with your personal data was sent to your e-mail address.'));
        }

        $content = $this->setting->getForDomain(Setting::PERSONAL_DATA_DISPLAY_SITE_CONTENT, $this->domain->getId());

        return $this->render('@ShopsysShop/Front/Content/PersonalData/index.html.twig', [
            'personalDataSiteContent' => $content,
            'title' => t('Personal information overview'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function exportAction(Request $request)
    {
        $form = $this->createForm(
            PersonalDataFormType::class,
            $this->personalDataAccessRequestDataFactory->createForExport()
        );

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $personalData = $this->personalDataAccessRequestFacade->createPersonalDataAccessRequest(
                $form->getData(),
                $this->domain->getId()
            );
            $this->personalDataAccessMailFacade->sendMail($personalData);
            $this->getFlashMessageSender()->addSuccessFlash(t('E-mail with a link to the export of your personal data was sent to your e-mail address.'));
        }

        $content = $this->setting->getForDomain(Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT, $this->domain->getId());

        return $this->render('@ShopsysShop/Front/Content/PersonalData/index.html.twig', [
            'personalDataSiteContent' => $content,
            'title' => t('Personal information export'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $hash
     */
    public function accessDisplayAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findByHashAndDomainId(
            $hash,
            $this->domain->getId()
        );

        if ($personalDataAccessRequest !== null && $personalDataAccessRequest->getType() === PersonalDataAccessRequest::TYPE_DISPLAY) {
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
     * @param string $hash
     */
    public function accessExportAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findByHashAndDomainId(
            $hash,
            $this->domain->getId()
        );

        if ($personalDataAccessRequest !== null && $personalDataAccessRequest->getType() === PersonalDataAccessRequest::TYPE_EXPORT) {
            $user = $this->customerFacade->findUserByEmailAndDomain($personalDataAccessRequest->getEmail(), $this->domain->getId());

            $newsletterSubscriber = $this->newsletterFacade->findNewsletterSubscriberByEmailAndDomainId(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );

            $ordersCount = $this->orderFacade->getOrdersCountByEmailAndDomainId(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );

            return $this->render('@ShopsysShop/Front/Content/PersonalData/export.html.twig', [
                'personalDataAccessRequest' => $personalDataAccessRequest,
                'domainName' => $this->domain->getName(),
                'hash' => $hash,
                'user' => $user,
                'newsletterSubscriber' => $newsletterSubscriber,
                'ordersCount' => $ordersCount,
            ]);
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param string $hash
     */
    public function exportXmlAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findByHashAndDomainId(
            $hash,
            $this->domain->getId()
        );

        if ($personalDataAccessRequest !== null && $personalDataAccessRequest->getType() === PersonalDataAccessRequest::TYPE_EXPORT) {
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

            $xmlContent = $this->render('@ShopsysShop/Front/Content/PersonalData/export.xml.twig', [
                'user' => $user,
                'newsletterSubscriber' => $newsletterSubscriber,
                'orders' => $orders,

            ])->getContent();

            $fileName = $personalDataAccessRequest->getEmail() . '.xml';

            return $this->xmlResponse->getXmlResponse($fileName, $xmlContent);
        }

        throw new NotFoundHttpException();
    }
}
