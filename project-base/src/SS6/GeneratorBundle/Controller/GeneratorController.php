<?php

namespace SS6\GeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GeneratorController extends Controller {

	public function indexAction() {
		return $this->render('@SS6Generator/index.html.twig');
	}

}
