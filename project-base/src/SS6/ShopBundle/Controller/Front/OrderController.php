<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends Controller {

	public function indexAction() {		
		return $this->render('@SS6Shop/Front/Content/Order/index.html.twig');
	}

	public function personalAction() {		
		return $this->render('@SS6Shop/Front/Content/Order/personal.html.twig');
	}
}
