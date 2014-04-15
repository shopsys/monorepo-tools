<?php

namespace SS6\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TransportAndPaymentController extends Controller {
	
	public function indexAction() {
		return $this->render('SS6ShopBundle::Admin/Content/TransportAndPayment/index.html.twig');
	}
	
	public function transportListAction() {
		$transportRepository = $this->get('ss6.core.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentRepository = $this->get('ss6.core.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		
		$allPayments = $paymentRepository->getAllWithTransports();
		$transports = $transportRepository->getAllDataWithVisibility($allPayments);
		
		return $this->render('SS6ShopBundle::Admin/Content/TransportAndPayment/transportList.html.twig', array(
			'transports' => $transports,
		));
	}
	
	public function paymentListAction() {
		$paymentRepository = $this->get('ss6.core.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$payments = $paymentRepository->getAll();
		
		return $this->render('SS6ShopBundle::Admin/Content/TransportAndPayment/paymentList.html.twig', array(
			'payments' => $payments,
		));
	}
}
