<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BasketController extends Controller {

	public function indexAction() {		
		return $this->render('@SS6Shop/Front/Content/Basket/index.html.twig');
	}
}
