<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	/**
	 * @Route("/dashboard/")
	 */
	public function dashboardAction() {
		return $this->render('@SS6Shop/Admin/Content/Default/index.html.twig');
	}

}
