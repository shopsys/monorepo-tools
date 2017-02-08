<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\HttpFoundation\DownloadFileResponse;
use Shopsys\ShopBundle\Form\Front\Order\OrderFlow;
use Shopsys\ShopBundle\Model\Cart\CartFacade;
use Shopsys\ShopBundle\Model\Country\CountryFacade;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\ShopBundle\Model\Order\FrontOrderData;
use Shopsys\ShopBundle\Model\Order\Mail\OrderMailFacade;
use Shopsys\ShopBundle\Model\Order\OrderData;
use Shopsys\ShopBundle\Model\Order\OrderDataMapper;
use Shopsys\ShopBundle\Model\Order\OrderFacade;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreview;
use Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory;
use Shopsys\ShopBundle\Model\Order\Watcher\TransportAndPaymentWatcherService;
use Shopsys\ShopBundle\Model\Payment\PaymentEditFacade;
use Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade;
use Shopsys\ShopBundle\Model\Transport\TransportEditFacade;
use Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class OrderController extends FrontBaseController {

	const SESSION_CREATED_ORDER = 'created_order_id';

	/**
	 * @var \Shopsys\ShopBundle\Form\Front\Order\OrderFlow
	 */
	private $flow;

	/**
	 * @var \Shopsys\ShopBundle\Model\Cart\CartFacade
	 */
	private $cartFacade;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Mail\OrderMailFacade
	 */
	private $orderMailFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\OrderDataMapper
	 */
	private $orderDataMapper;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\OrderFacade
	 */
	private $orderFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Preview\OrderPreviewFactory
	 */
	private $orderPreviewFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\Watcher\TransportAndPaymentWatcherService
	 */
	private $transportAndPaymentWatcherService;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentEditFacade
	 */
	private $paymentEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyFacade
	 */
	private $currencyFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportEditFacade
	 */
	private $transportEditFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \Symfony\Component\HttpFoundation\Session\Session
	 */
	private $session;

	/**
	 * @var \Shopsys\ShopBundle\Model\TermsAndConditions\TermsAndConditionsFacade
	 */
	private $termsAndConditionsFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Newsletter\NewsletterFacade
	 */
	private $newsletterFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Country\CountryFacade
	 */
	private $countryFacade;

	public function __construct(
		OrderFacade $orderFacade,
		CartFacade $cartFacade,
		OrderPreviewFactory $orderPreviewFactory,
		TransportPriceCalculation $transportPriceCalculation,
		PaymentPriceCalculation $paymentPriceCalculation,
		Domain $domain,
		TransportEditFacade $transportEditFacade,
		PaymentEditFacade $paymentEditFacade,
		CurrencyFacade $currencyFacade,
		OrderDataMapper $orderDataMapper,
		OrderFlow $flow,
		Session $session,
		TransportAndPaymentWatcherService $transportAndPaymentWatcherService,
		OrderMailFacade $orderMailFacade,
		TermsAndConditionsFacade $termsAndConditionsFacade,
		NewsletterFacade $newsletterFacade,
		CountryFacade $countryFacade
	) {
		$this->orderFacade = $orderFacade;
		$this->cartFacade = $cartFacade;
		$this->orderPreviewFactory = $orderPreviewFactory;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
		$this->domain = $domain;
		$this->transportEditFacade = $transportEditFacade;
		$this->paymentEditFacade = $paymentEditFacade;
		$this->currencyFacade = $currencyFacade;
		$this->orderDataMapper = $orderDataMapper;
		$this->flow = $flow;
		$this->session = $session;
		$this->transportAndPaymentWatcherService = $transportAndPaymentWatcherService;
		$this->orderMailFacade = $orderMailFacade;
		$this->termsAndConditionsFacade = $termsAndConditionsFacade;
		$this->newsletterFacade = $newsletterFacade;
		$this->countryFacade = $countryFacade;
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function indexAction() {
		$flashMessageBag = $this->get('ss6.shop.component.flash_message.bag.front');
		/* @var $flashMessageBag \Shopsys\ShopBundle\Component\FlashMessage\Bag */

		$cart = $this->cartFacade->getCartOfCurrentCustomer();
		if ($cart->isEmpty()) {
			return $this->redirectToRoute('front_cart');
		}

		$payments = $this->paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $this->transportEditFacade->getVisibleOnCurrentDomain($payments);
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

		if ($this->flow->isBackToCartTransition()) {
			return $this->redirectToRoute('front_cart');
		}
		$countries = $this->countryFacade->getAllOnCurrentDomain();

		$this->flow->setFormTypesData($transports, $payments, $countries);
		$this->flow->bind($frontOrderFormData);
		$this->flow->saveSentStepData();

		$form = $this->flow->createForm();

		$payment = $frontOrderFormData->payment;
		$transport = $frontOrderFormData->transport;

		$orderPreview = $this->orderPreviewFactory->createForCurrentUser($transport, $payment);

		$isValid = $this->flow->isValid($form);
		// FormData are filled during isValid() call
		$orderData = $this->orderDataMapper->getOrderDataFromFrontOrderData($frontOrderFormData);

		$this->checkTransportAndPaymentChanges($orderData, $orderPreview, $transports, $payments);

		if ($isValid) {
			if ($this->flow->nextStep()) {
				$form = $this->flow->createForm();
			} elseif ($flashMessageBag->isEmpty()) {
				$order = $this->orderFacade->createOrderFromFront($orderData);

				if ($frontOrderFormData->newsletterSubscription) {
					$this->newsletterFacade->addSubscribedEmail($frontOrderFormData->email);
				}

				$this->flow->reset();

				try {
					$this->sendMail($order);
				} catch (\Shopsys\ShopBundle\Model\Mail\Exception\SendMailFailedException $e) {
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

		return $this->render('@SS6Shop/Front/Content/Order/index.html.twig', [
			'form' => $form->createView(),
			'flow' => $this->flow,
			'transport' => $transport,
			'payment' => $payment,
			'payments' => $payments,
			'transportsPrices' => $this->transportPriceCalculation->calculatePricesById(
				$transports,
				$currency,
				$orderPreview->getProductsPrice(),
				$domainId
			),
			'paymentsPrices' => $this->paymentPriceCalculation->calculatePricesById(
				$payments,
				$currency,
				$orderPreview->getProductsPrice(),
				$domainId
			),
			'termsAndConditionsArticle' => $this->termsAndConditionsFacade->findTermsAndConditionsArticleByDomainId(
				$this->domain->getId()
			),
		]);
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function previewAction(Request $request) {
		$transportId = $request->get('transportId');
		$paymentId = $request->get('paymentId');

		if ($transportId === null) {
			$transport = null;
		} else {
			$transport = $this->transportEditFacade->getById($transportId);
		}

		if ($paymentId === null) {
			$payment = null;
		} else {
			$payment = $this->paymentEditFacade->getById($paymentId);
		}

		$orderPreview = $this->orderPreviewFactory->createForCurrentUser($transport, $payment);

		return $this->render('@SS6Shop/Front/Content/Order/preview.html.twig', [
			'orderPreview' => $orderPreview,
		]);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Order\OrderData $orderData
	 * @param \Shopsys\ShopBundle\Model\Order\Preview\OrderPreview $orderPreview
	 * @param \Shopsys\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \Shopsys\ShopBundle\Model\Payment\Payment[] $payments
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

	public function saveOrderFormAction() {
		$payments = $this->paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $this->transportEditFacade->getVisibleOnCurrentDomain($payments);
		$countries = $this->countryFacade->getAllOnCurrentDomain();

		$this->flow->setFormTypesData($transports, $payments, $countries);
		$this->flow->bind(new FrontOrderData());
		$form = $this->flow->createForm();
		$this->flow->saveCurrentStepData($form);

		return new Response();
	}

	public function sentAction() {
		$orderId = $this->session->get(self::SESSION_CREATED_ORDER, null);
		$this->session->remove(self::SESSION_CREATED_ORDER);

		if ($orderId === null) {
			return $this->redirectToRoute('front_cart');
		}

		return $this->render('@SS6Shop/Front/Content/Order/sent.html.twig', [
			'orderConfirmationText' => $this->orderFacade->getOrderConfirmText($orderId),
			'order' => $this->orderFacade->getById($orderId),
		]);
	}

	public function termsAndConditionsAction() {
		return $this->getTermsAndConditionsResponse();
	}

	public function termsAndConditionsDownloadAction() {
		$response = $this->getTermsAndConditionsResponse();

		return new DownloadFileResponse(
			$this->termsAndConditionsFacade->getDownloadFilename(),
			$response->getContent()
		);
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	private function getTermsAndConditionsResponse() {
		return $this->render('@SS6Shop/Front/Content/Order/termsAndConditions.html.twig', [
			'termsAndConditionsArticle' => $this->termsAndConditionsFacade->findTermsAndConditionsArticleByDomainId(
				$this->domain->getId()
			),
		]);
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Order\Order $order
	 */
	private function sendMail($order) {
		$mailTemplate = $this->orderMailFacade->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
		if ($mailTemplate->isSendMail()) {
			$this->orderMailFacade->sendEmail($order);
		}
	}

}
