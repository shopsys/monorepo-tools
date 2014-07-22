<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SuperadminController extends Controller {

	/**
	 * @Route("/superadmin/")
	 */
	public function indexAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/index.html.twig');
	}

	/**
	 * @Route("/superadmin/icons/")
	 */
	public function iconsAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/icons.html.twig');
	}

	/**
	 * @Route("/superadmin/errors/")
	 */
	public function errorsAction() {
		return $this->render('@SS6Shop/Admin/Content/Superadmin/errors.html.twig');
	}

}
