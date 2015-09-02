<?php

namespace SS6\ShopBundle\Component\System;

class System {

	/**
	 * @return bool
	 */
	public function isWindows() {
		return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
	}
}
