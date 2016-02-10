<?php

namespace SS6\ShopBundle\Component\Breadcrumb\Exception;

use Exception;
use SS6\ShopBundle\Component\Breadcrumb\Exception\BreadcrumbException;

class BreadcrumbGeneratorNotFoundException extends Exception implements BreadcrumbException {

	/**
	 * @param string $routeName
	 * @param \Exception|null $previous
	 */
	public function __construct($routeName, Exception $previous = null) {
		parent::__construct('Breadcrumb generator not found for route "' . $routeName . '"', 0, $previous);
	}

}
