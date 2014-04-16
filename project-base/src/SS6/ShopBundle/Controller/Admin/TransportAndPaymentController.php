<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TransportAndPaymentController extends Controller {
	
	/**
	 * @Route("/transport_and_payment/list/")
	 */
	public function listAction() {
		return $this->render('@SS6Shop/Admin/Content/TransportAndPayment/list.html.twig');
	}
	
}
