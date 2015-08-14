<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Controller\Admin\BaseController;

class DefaultController extends BaseController {

	/**
	 * @Route("/dashboard/")
	 */
	public function dashboardAction() {
		return $this->render('@SS6Shop/Admin/Content/Default/index.html.twig');
	}

}
