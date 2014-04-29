<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Form\Front\Order\OrderFormData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
		$flow->setFormTypesData($transports, $payments);
		$flow->bind($formData);

		$form = $flow->createForm(array('validation_groups' => array('Default')));

		if ($flow->isValid($form)) {
			$flow->saveCurrentStepData($form);

			if ($flow->nextStep()) {
				$form = $flow->createForm();
			} else {
				// save

				$flow->reset();

				return $this->redirect($this->generateUrl('front_basket_index'));
			}
		}

		return $this->render('@SS6Shop/Front/Content/Order/index.html.twig', array(
			'form' => $form->createView(),
			'flow' => $flow,
			'payments' => $payments,
		));
	}

	public function personalAction() {
		return $this->render('@SS6Shop/Front/Content/Order/personal.html.twig');
	}

	public function sendedAction() {
		return $this->render('@SS6Shop/Front/Content/Order/sended.html.twig');
	}

}
