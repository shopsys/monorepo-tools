<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SliderController extends Controller {
	
	/**
	 * @Route("/slider/list/")
	 */
	public function listAction() {
		return $this->render('@SS6Shop/Admin/Content/Slider/list.html.twig');
	}
}
