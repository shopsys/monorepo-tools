<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Order\OrderFormData;
use SS6\ShopBundle\Model\Customer\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

class OrderController extends Controller {

	public function indexAction() {
		$paymentRepository = $this->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */

		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */

		$orderFacade = $this->get('ss6.shop.order.order_facade');
		/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */

		$payments = $paymentRepository->getVisible();
		$transports = $transportRepository->getVisible($payments);
		$user = $this->getUser();

		$formData = new OrderFormData();
		if ($user instanceof User) {
			$orderFacade->prefillOrderFormData($formData, $user);
		}

		$flow = $this->get('ss6.shop.order.flow');
		/* @var $flow \SS6\ShopBundle\Form\Front\Order\OrderFlow */

		$flow->setFormTypesData($transports, $payments);
		$flow->saveSentStepData();

		if ($flow->isBackToCartTransition()) {
			return $this->redirect($this->generateUrl('front_cart'));
		}

		$flow->bind($formData);

		// validate all constraints (not only step specific group)
		$form = $flow->createForm(array('validation_groups' => array('Default')));

		if ($flow->isValid($form)) {
			if ($flow->nextStep()) {
				$form = $flow->createForm();
			} else {
				$orderFacade->createOrder($formData, $this->getUser());

				$flow->reset();

				return $this->redirect($this->generateUrl('front_order_sent'));
			}
		}

		if ($form->isSubmitted() && !$form->isValid() && empty($form->getErrors())) {
			$form->addError(new FormError('Prosím zkontrolujte si správnost vyplnění všech údajů'));
		}

		return $this->render('@SS6Shop/Front/Content/Order/index.html.twig', array(
			'form' => $form->createView(),
			'flow' => $flow,
			'payments' => $payments,
		));
	}

	public function sentAction() {
		return $this->render('@SS6Shop/Front/Content/Order/sent.html.twig');
	}

}
