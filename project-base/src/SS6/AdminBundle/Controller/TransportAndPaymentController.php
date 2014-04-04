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
		$transports = $transportRepository->getAllUndeleted();
		
		return $this->render('SS6AdminBundle:Content:TransportAndPayment/transportList.html.twig', array(
			'transports' => $transports,
		));
	}
}
