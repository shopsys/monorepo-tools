<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends Controller {

	public function indexAction() {
		$paymentRepository = $this->get('ss6.shop.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */

		$transportRepository = $this->get('ss6.shop.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		
		$payments = $paymentRepository->getVisible();
		$transports = $transportRepository->getVisible($payments);

		return $this->render('@SS6Shop/Front/Content/Order/index.html.twig', array(
			'payments' => $payments,
			'transports' => $transports,
		));
	}

	public function personalAction() {		
		return $this->render('@SS6Shop/Front/Content/Order/personal.html.twig');
	}

	public function sendedAction() {		
		return $this->render('@SS6Shop/Front/Content/Order/sended.html.twig');
	}
}
