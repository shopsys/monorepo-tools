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
	
}
