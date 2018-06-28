<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\HttpFoundation\DownloadFileResponse;
use Shopsys\FrameworkBundle\Model\Cart\CartFacade;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\LegalConditions\LegalConditionsFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Order\FrontOrderData;
use Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderDataMapper;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\FrameworkBundle\Model\Order\Watcher\TransportAndPaymentWatcherService;
use Shopsys\FrameworkBundle\Model\Payment\PaymentFacade;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportFacade;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\ShopBundle\Form\Front\Order\DomainAwareOrderFlowFactory;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends FrontBaseController
{
    const SESSION_CREATED_ORDER = 'created_order_id';

    /**
     * @var \Shopsys\ShopBundle\Form\Front\Order\DomainAwareOrderFlowFactory
     */
    private $domainAwareOrderFlowFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\CartFacade
     */
    private $cartFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Mail\OrderMailFacade
     */
    private $orderMailFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderDataMapper
     */
    private $orderDataMapper;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory
     */
    private $orderPreviewFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\Watcher\TransportAndPaymentWatcherService
     */
    private $transportAndPaymentWatcherService;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade
     */
    private $paymentFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation
     */
    private $paymentPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    private $currencyFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportFacade
     */
    private $transportFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation
     */
    private $transportPriceCalculation;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    private $session;

    /**
     * @var \Shopsys\FrameworkBundle\Model\LegalConditions\LegalConditionsFacade
     */
    private $legalConditionsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade
     */
    private $newsletterFacade;

    public function __construct(
        OrderFacade $orderFacade,
        CartFacade $cartFacade,
        OrderPreviewFactory $orderPreviewFactory,
        TransportPriceCalculation $transportPriceCalculation,
        PaymentPriceCalculation $paymentPriceCalculation,
        Domain $domain,
        TransportFacade $transportFacade,
        PaymentFacade $paymentFacade,
        CurrencyFacade $currencyFacade,
        OrderDataMapper $orderDataMapper,
        DomainAwareOrderFlowFactory $domainAwareOrderFlowFactory,
        SessionInterface $session,
        TransportAndPaymentWatcherService $transportAndPaymentWatcherService,
        OrderMailFacade $orderMailFacade,
        LegalConditionsFacade $legalConditionsFacade,
        NewsletterFacade $newsletterFacade
    ) {
        $this->orderFacade = $orderFacade;
        $this->cartFacade = $cartFacade;
        $this->orderPreviewFactory = $orderPreviewFactory;
        $this->transportPriceCalculation = $transportPriceCalculation;
        $this->paymentPriceCalculation = $paymentPriceCalculation;
        $this->domain = $domain;
        $this->transportFacade = $transportFacade;
        $this->paymentFacade = $paymentFacade;
        $this->currencyFacade = $currencyFacade;
        $this->orderDataMapper = $orderDataMapper;
        $this->domainAwareOrderFlowFactory = $domainAwareOrderFlowFactory;
        $this->session = $session;
        $this->transportAndPaymentWatcherService = $transportAndPaymentWatcherService;
        $this->orderMailFacade = $orderMailFacade;
        $this->legalConditionsFacade = $legalConditionsFacade;
        $this->newsletterFacade = $newsletterFacade;
    }

    public function indexAction()
    {
        $flashMessageBag = $this->get('shopsys.shop.component.flash_message.bag.front');
        /* @var $flashMessageBag \Shopsys\FrameworkBundle\Component\FlashMessage\Bag */

        $cart = $this->cartFacade->getCartOfCurrentCustomer();
        if ($cart->isEmpty()) {
            return $this->redirectToRoute('front_cart');
        }

        $user = $this->getUser();

        $frontOrderFormData = new FrontOrderData();
        $frontOrderFormData->deliveryAddressSameAsBillingAddress = true;
        if ($user instanceof User) {
            $this->orderFacade->prefillFrontOrderData($frontOrderFormData, $user);
        }
        $domainId = $this->domain->getId();
        $frontOrderFormData->domainId = $domainId;
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
        $frontOrderFormData->currency = $currency;

        $orderFlow = $this->domainAwareOrderFlowFactory->create();
        if ($orderFlow->isBackToCartTransition()) {
            return $this->redirectToRoute('front_cart');
        }

        $orderFlow->bind($frontOrderFormData);
        $orderFlow->saveSentStepData();

        $form = $orderFlow->createForm();

        $payment = $frontOrderFormData->payment;
        $transport = $frontOrderFormData->transport;

        $orderPreview = $this->orderPreviewFactory->createForCurrentUser($transport, $payment);

        $isValid = $orderFlow->isValid($form);
        // FormData are filled during isValid() call
        $orderData = $this->orderDataMapper->getOrderDataFromFrontOrderData($frontOrderFormData);

        $payments = $this->paymentFacade->getVisibleOnCurrentDomain();
        $transports = $this->transportFacade->getVisibleOnCurrentDomain($payments);
        $this->checkTransportAndPaymentChanges($orderData, $orderPreview, $transports, $payments);

        if ($isValid) {
            if ($orderFlow->nextStep()) {
                $form = $orderFlow->createForm();
            } elseif ($flashMessageBag->isEmpty()) {
                $order = $this->orderFacade->createOrderFromFront($orderData);
                $this->orderFacade->sendHeurekaOrderInfo($order, $frontOrderFormData->disallowHeurekaVerifiedByCustomers);

                if ($frontOrderFormData->newsletterSubscription) {
                    $this->newsletterFacade->addSubscribedEmail($frontOrderFormData->email, $this->domain->getId());
                }

                $orderFlow->reset();

                try {
                    $this->sendMail($order);
                } catch (\Shopsys\FrameworkBundle\Model\Mail\Exception\MailException $e) {
                    $this->getFlashMessageSender()->addErrorFlash(
                        t('Unable to send some e-mails, please contact us for order verification.')
                    );
                }

                $this->session->set(self::SESSION_CREATED_ORDER, $order->getId());

                return $this->redirectToRoute('front_order_sent');
            }
        }

        if ($form->isSubmitted() && !$form->isValid() && $form->getErrors()->count() === 0) {
            $form->addError(new FormError(t('Please check the correctness of all data filled.')));
        }

        return $this->render('@ShopsysShop/Front/Content/Order/index.html.twig', [
            'form' => $form->createView(),
            'flow' => $orderFlow,
            'transport' => $transport,
            'payment' => $payment,
            'payments' => $payments,
            'transportsPrices' => $this->transportPriceCalculation->getCalculatedPricesIndexedByTransportId(
                $transports,
                $currency,
                $orderPreview->getProductsPrice(),
                $domainId
            ),
            'paymentsPrices' => $this->paymentPriceCalculation->getCalculatedPricesIndexedByPaymentId(
                $payments,
                $currency,
                $orderPreview->getProductsPrice(),
                $domainId
            ),
            'termsAndConditionsArticle' => $this->legalConditionsFacade->findTermsAndConditions($this->domain->getId()),
            'privacyPolicyArticle' => $this->legalConditionsFacade->findPrivacyPolicy($this->domain->getId()),
        ]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function previewAction(Request $request)
    {
        $transportId = $request->get('transportId');
        $paymentId = $request->get('paymentId');

        if ($transportId === null) {
            $transport = null;
        } else {
            $transport = $this->transportFacade->getById($transportId);
        }

        if ($paymentId === null) {
            $payment = null;
        } else {
            $payment = $this->paymentFacade->getById($paymentId);
        }

        $orderPreview = $this->orderPreviewFactory->createForCurrentUser($transport, $payment);

        return $this->render('@ShopsysShop/Front/Content/Order/preview.html.twig', [
            'orderPreview' => $orderPreview,
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\OrderData $orderData
     * @param \Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreview $orderPreview
     * @param \Shopsys\FrameworkBundle\Model\Transport\Transport[] $transports
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment[] $payments
     */
    private function checkTransportAndPaymentChanges(
        OrderData $orderData,
        OrderPreview $orderPreview,
        array $transports,
        array $payments
    ) {
        $transportAndPaymentCheckResult = $this->transportAndPaymentWatcherService->checkTransportAndPayment(
            $orderData,
            $orderPreview,
            $transports,
            $payments
        );

        if ($transportAndPaymentCheckResult->isTransportPriceChanged()) {
            $this->getFlashMessageSender()->addInfoFlashTwig(
                t('The price of shipping {{ transportName }} changed during ordering process. Check your order, please.'),
                [
                    'transportName' => $orderData->transport->getName(),
                ]
            );
        }
        if ($transportAndPaymentCheckResult->isPaymentPriceChanged()) {
            $this->getFlashMessageSender()->addInfoFlashTwig(
                t('The price of payment {{ transportName }} changed during ordering process. Check your order, please.'),
                [
                    'paymentName' => $orderData->payment->getName(),
                ]
            );
        }
    }

    public function saveOrderFormAction()
    {
        $flow = $this->domainAwareOrderFlowFactory->create();
        $flow->bind(new FrontOrderData());
        $form = $flow->createForm();
        $flow->saveCurrentStepData($form);

        return new Response();
    }

    public function sentAction()
    {
        $orderId = $this->session->get(self::SESSION_CREATED_ORDER, null);
        $this->session->remove(self::SESSION_CREATED_ORDER);

        if ($orderId === null) {
            return $this->redirectToRoute('front_cart');
        }

        return $this->render('@ShopsysShop/Front/Content/Order/sent.html.twig', [
            'pageContent' => $this->orderFacade->getOrderSentPageContent($orderId),
            'order' => $this->orderFacade->getById($orderId),
        ]);
    }

    public function termsAndConditionsAction()
    {
        return $this->getTermsAndConditionsResponse();
    }

    public function termsAndConditionsDownloadAction()
    {
        $response = $this->getTermsAndConditionsResponse();

        return new DownloadFileResponse(
            $this->legalConditionsFacade->getTermsAndConditionsDownloadFilename(),
            $response->getContent()
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function getTermsAndConditionsResponse()
    {
        return $this->render('@ShopsysShop/Front/Content/Order/legalConditions.html.twig', [
            'termsAndConditionsArticle' => $this->legalConditionsFacade->findTermsAndConditions($this->domain->getId()),
        ]);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Order\Order $order
     */
    private function sendMail($order)
    {
        $mailTemplate = $this->orderMailFacade->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
        if ($mailTemplate->isSendMail()) {
            $this->orderMailFacade->sendEmail($order);
        }
    }
}
