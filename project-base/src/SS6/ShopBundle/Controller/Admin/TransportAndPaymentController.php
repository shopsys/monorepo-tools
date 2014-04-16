<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TransportAndPaymentController extends Controller {
	
	/**
	 * @Route("transport_and_payment/list/", name="admin_transport_and_payment_list")
	 */
	public function indexAction() {
		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/index.html.twig');
	}
	
	/**
	 * @Route("transport/list/", name="admin_transport_list")
	 */
	public function transportListAction() {
		$transportRepository = $this->get('ss6.core.transport.transport_repository');
		/* @var $transportRepository \SS6\ShopBundle\Model\Transport\TransportRepository */
		$paymentRepository = $this->get('ss6.core.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		
		$allPayments = $paymentRepository->getAllWithTransports();
		$transports = $transportRepository->getAllDataWithVisibility($allPayments);
		
		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/transportList.html.twig', array(
			'transports' => $transports,
		));
	}
	
	/**
	 * @Route("payment/list/", name="admin_payment_list")
	 */
	public function paymentListAction() {
		$paymentRepository = $this->get('ss6.core.payment.payment_repository');
		/* @var $paymentRepository \SS6\ShopBundle\Model\Payment\PaymentRepository */
		$payments = $paymentRepository->getAll();
		
		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/paymentList.html.twig', array(
			'payments' => $payments,
		));
	}
}
