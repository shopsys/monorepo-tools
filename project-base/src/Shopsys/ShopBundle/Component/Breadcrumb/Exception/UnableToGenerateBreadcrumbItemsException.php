<?php

namespace Shopsys\ShopBundle\Component\Breadcrumb\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Breadcrumb\Exception\BreadcrumbException;

class UnableToGenerateBreadcrumbItemsException extends Exception implements BreadcrumbException {

	/**
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct($message = '', Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}

}
