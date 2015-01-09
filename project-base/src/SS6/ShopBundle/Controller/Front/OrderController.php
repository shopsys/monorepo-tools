<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use SS6\ShopBundle\Model\Setting\Setting;

class OrderController extends Controller {

	const SESSION_CREATED_ORDER = 'created_order_id';

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function indexAction() {
		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
		$cartFacade = $this->get('ss6.shop.cart.cart_facade');
		/* @var $cartFacade \SS6\ShopBundle\Model\Cart\CartFacade */
		$cart = $this->get('ss6.shop.cart');
		/* @var $cart \SS6\ShopBundle\Model\Cart\Cart */
		$orderPreviewCalculation = $this->get('ss6.shop.order.preview.order_preview_calculation');
		/* @var $orderPreviewCalculation \SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$flashMessageBag = $this->get('ss6.shop.flash_message.bag.front');
		/* @var $flashMessageBag \SS6\ShopBundle\Model\FlashMessage\Bag */
		$customerEditFacade = $this->get('ss6.shop.customer.customer_edit_facade');
		/* @var $customerEditFacade \SS6\ShopBundle\Model\Customer\CustomerEditFacade */
		$transportPriceCalculation = $this->get('ss6.shop.transport.transport_price_calculation');
		/* @var $transportPriceCalculation \SS6\ShopBundle\Model\Transport\TransportPriceCalculation */
		$paymentPriceCalculation = $this->get('ss6.shop.payment.payment_price_calculation');
		/* @var $paymentPriceCalculation \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */
		$currencyFacade = $this->get('ss6.shop.pricing.currency.currency_facade');
		/* @var $currencyFacade \SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade */

		if ($cart->isEmpty()) {
			return $this->redirect($this->generateUrl('front_cart'));
		}

		$payments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $transportEditFacade->getVisibleOnCurrentDomain($payments);
		$user = $this->getUser();

		$orderData = new OrderData();
		if ($user instanceof User) {
			$orderFacade->prefillOrderData($orderData, $user);
		}
		$domainId = $domain->getId();
		$orderData->domainId = $domainId;
		$currency = $currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);
		$orderData->currency = $currency;

		$flow = $this->get('ss6.shop.order.flow');
		/* @var $flow \SS6\ShopBundle\Form\Front\Order\OrderFlow */

		if ($flow->isBackToCartTransition()) {
			return $this->redirect($this->generateUrl('front_cart'));
		}

		$flow->setFormTypesData($transports, $payments);
		$flow->bind($orderData);
		$flow->saveSentStepData();

		$form = $flow->createForm();

		$this->checkTransportAndPaymentChanges($orderData, $transports, $payments);

		if ($flow->isValid($form)) {
			if ($flow->nextStep()) {
				$form = $flow->createForm();
			} elseif ($flashMessageBag->isEmpty()) {
				$order = $orderFacade->createOrderFromCart($orderData, $this->getUser());
				$cartFacade->cleanCart();
				if ($user instanceof User) {
					$customerEditFacade->amendCustomerDataFromOrder($user, $order);
				}

				$flow->reset();

				try {
					$this->sendMail($order);
				} catch (\SS6\ShopBundle\Model\Order\Mail\Exception\SendMailFailedException $e) {
					$flashMessageSender->addErrorFlash('Nepodařilo se odeslat některé emaily, pro ověření objednávky nás prosím kontaktujte.');
				}

				$session = $this->get('session');
				/* @var $session \Symfony\Component\HttpFoundation\Session\Session */
				$session->set(self::SESSION_CREATED_ORDER, $order->getId());

				return $this->redirect($this->generateUrl('front_order_sent'));
			}
		}

		if ($form->isSubmitted() && !$form->isValid() && $form->getErrors()->count() === 0) {
			$form->addError(new FormError('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		$payment = $orderData->getPayment();
		$transport = $orderData->getTransport();

		return $this->render('@SS6Shop/Front/Content/Order/index.html.twig', array(
			'form' => $form->createView(),
			'flow' => $flow,
			'orderPreview' => $orderPreviewCalculation->calculatePreview($cart, $transport, $payment),
			'payments' => $payments,
			'transportsPrices' => $transportPriceCalculation->calculatePricesById($transports),
			'paymentsPrices' => $paymentPriceCalculation->calculatePricesById($payments),
		));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\OrderData $orderData
	 * @param \SS6\ShopBundle\Model\Transport\Transport[] $transports
	 * @param \SS6\ShopBundle\Model\Payment\Payment[] $payments
	 */
	private function checkTransportAndPaymentChanges(
		OrderData $orderData,
		array $transports,
		array $payments
	) {
		$transportAndPaymentWatcherService = $this->get('ss6.shop.order.order_transport_and_payment_watcher_service');
		/* @var $transportAndPaymentWatcherService \SS6\ShopBundle\Model\Order\Watcher\TransportAndPaymentWatcherService */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.front');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$transportAndPaymentCheckResult = $transportAndPaymentWatcherService->checkTransportAndPayment(
			$orderData,
			$transports,
			$payments
		);

		if ($transportAndPaymentCheckResult->isTransportPriceChanged()) {
			$flashMessageSender->addInfoTwig(
				'V průběhu objednávkového procesu byla změněna cena dopravy {{ transportName }}. Prosím, překontrolujte si objednávku.',
				array(
					'transportName' => $orderData->transport->getName(),
				)
			);
		}
		if ($transportAndPaymentCheckResult->isPaymentPriceChanged()) {
			$flashMessageSender->addInfoTwig(
				'V průběhu objednávkového procesu byla změněna cena platby {{ paymentName }}. Prosím, překontrolujte si objednávku.',
				array(
					'paymentName' => $orderData->payment->getName(),
				)
			);
		}
	}

	public function saveOrderFormAction() {
		$flow = $this->get('ss6.shop.order.flow');
		/* @var $flow \SS6\ShopBundle\Form\Front\Order\OrderFlow */
		$transportEditFacade = $this->get('ss6.shop.transport.transport_edit_facade');
		/* @var $transportEditFacade \SS6\ShopBundle\Model\Transport\TransportEditFacade */
		$paymentEditFacade = $this->get('ss6.shop.payment.payment_edit_facade');
		/* @var $paymentEditFacade \SS6\ShopBundle\Model\Payment\PaymentEditFacade */

		$payments = $paymentEditFacade->getVisibleOnCurrentDomain();
		$transports = $transportEditFacade->getVisibleOnCurrentDomain($payments);

		$flow->setFormTypesData($transports, $payments);
		$flow->bind(new OrderData());
		$form = $flow->createForm();
		$flow->saveCurrentStepData($form);

		return new Response();
	}

	public function sentAction() {
		$session = $this->get('session');
		/* @var $session \Symfony\Component\HttpFoundation\Session\Session */
		$setting = $this->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */

		$orderId = $session->get(self::SESSION_CREATED_ORDER, null);
		$session->remove(self::SESSION_CREATED_ORDER);

		if ($orderId === null) {
			return $this->redirect($this->generateUrl('front_cart'));
		}
		$orderConfirmationText = $setting->get(Setting::ORDER_SUBMITTED_SETTING_NAME, $domain->getId());

		return $this->render('@SS6Shop/Front/Content/Order/sent.html.twig', array(
			'orderConfirmationText' => $orderConfirmationText,
		));
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Order $order
	 */
	private function sendMail($order) {
		$orderMailFacade = $this->get('ss6.shop.order.order_mail_facade');
		/* @var $orderMailFacade \SS6\ShopBundle\Model\Order\Mail\OrderMailFacade */
		$mailTemplate = $orderMailFacade->getMailTemplateByStatusAndDomainId($order->getStatus(), $order->getDomainId());
		if ($mailTemplate->isSendMail()) {
			$orderMailFacade->sendEmail($order);
		}
	}

}
