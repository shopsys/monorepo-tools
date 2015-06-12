<?php

namespace SS6\ShopBundle\Component\Breadcrumb\Exception;

use Exception;
use SS6\ShopBundle\Component\Breadcrumb\Exception\BreadcrumbException;

class UnableToGenerateBreadcrumbItemsException extends Exception implements BreadcrumbException {

	/**
	 * @param \Exception $previous
	 */
	public function __construct(Exception $previous = null) {
		parent::__construct(null, 0, $previous);
	}

}
