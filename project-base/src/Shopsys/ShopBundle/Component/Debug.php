<?php

namespace Shopsys\ShopBundle\Component;

use Doctrine\Common\Util\Debug as DoctrineDebug;

class Debug {

	/**
	 * @param mixed $var
	 * @return string
	 */
	public static function export($var) {
		return DoctrineDebug::dump($var, 2, true, false);
	}
}
