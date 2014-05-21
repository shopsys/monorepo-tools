<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Order\OrderFormData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

class OrderController extends Controller {

	public function indexAction() {
		$paymentRepository = $this->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */

		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */

		$payments = $paymentRepository->getVisible();
		$transports = $transportRepository->getVisible($payments);

		$formData = new OrderFormData();

		$flow = $this->get('ss6.shop.order.flow');
		/* @var $flow \SS6\ShopBundle\Form\Front\Order\OrderFlow */
		$flow->setFormTypesData($transports, $payments);
		$flow->bind($formData);

		// validate all constraints (not only step specific group)
		$form = $flow->createForm(array('validation_groups' => array('Default')));

		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData($form);

			if ($flow->nextStep()) {
				$form = $flow->createForm();
			} else {
				$orderFacade = $this->get('ss6.shop.order.order_facade');
				/* @var $orderFacade \SS6\ShopBundle\Model\Order\OrderFacade */
				$orderFacade->createOrder($formData, $this->getUser());

				$flow->reset();

				return $this->redirect($this->generateUrl('front_order_sent'));
			}
		} elseif ($form->isSubmitted()) {
			if (!$form->getErrors()) {
				$form->addError(new FormError('Prosím zkontrolujte si správnost vyplnění všech údajů'));
			}
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
