<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		return $this->render('@SS6Shop/Front/Content/Default/index.html.twig');
	}

}
