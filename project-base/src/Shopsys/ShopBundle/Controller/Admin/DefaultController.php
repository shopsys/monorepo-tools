<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;

class DefaultController extends AdminBaseController {

	/**
	 * @Route("/dashboard/")
	 */
	public function dashboardAction() {
		return $this->render('@SS6Shop/Admin/Content/Default/index.html.twig');
	}

}
