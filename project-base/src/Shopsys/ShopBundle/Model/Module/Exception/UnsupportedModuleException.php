<?php

namespace SS6\ShopBundle\Model\Module\Exception;

use Exception;

class UnsupportedModuleException extends Exception implements ModuleException {

	/**
	 * @param string $moduleName
	 * @param \Exception|null $previous
	 */
	public function __construct($moduleName, Exception $previous = null) {
		parent::__construct(sprintf('Module "%s" is not supported', $moduleName), 0, $previous);
	}
}
