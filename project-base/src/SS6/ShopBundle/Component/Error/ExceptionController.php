<?php

namespace SS6\ShopBundle\Component\Error;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;

class ExceptionController extends BaseController {

	/**
	 * @param bool $bool
	 */
	public function setDebug($bool) {
		$this->debug = $bool;
	}

	/**
	 * @return bool
	 */
	public function getDebug() {
		return $this->debug;
	}

}
