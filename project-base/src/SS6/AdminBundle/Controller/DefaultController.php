<?php

namespace SS6\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		return $this->render('SS6AdminBundle:Default:index.html.twig');
	}

}
