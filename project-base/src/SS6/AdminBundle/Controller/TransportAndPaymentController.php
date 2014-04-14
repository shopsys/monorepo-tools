<?php

namespace SS6\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TransportAndPaymentController extends Controller {
	
	public function indexAction() {
		return $this->render('SS6AdminBundle:Content:TransportAndPayment/index.html.twig');
	}
	
	public function transportListAction() {
		$transportRepository = $this->get('ss6.core.transport.transport_repository');
		/* @var $transportRepository \SS6\CoreBundle\Model\Transport\Repository\TransportRepository */
		$paymentRepository = $this->get('ss6.core.payment.payment_repository');
		/* @var $paymentRepository \SS6\CoreBundle\Model\Payment\Repository\PaymentRepository */
		
		$allPayments = $paymentRepository->getAllWithTransports();
		$transports = $transportRepository->getAllDataWithVisibility($allPayments);
		
		return $this->render('SS6AdminBundle:Content:TransportAndPayment/transportList.html.twig', array(
			'transports' => $transports,
		));
	}
	
	public function paymentListAction() {
		$paymentRepository = $this->get('ss6.core.payment.payment_repository');
		/* @var $paymentRepository \SS6\CoreBundle\Model\Payment\Repository\PaymentRepository */
		$payments = $paymentRepository->getAll();
		
		return $this->render('SS6AdminBundle:Content:TransportAndPayment/paymentList.html.twig', array(
			'payments' => $payments,
		));
	}
}
