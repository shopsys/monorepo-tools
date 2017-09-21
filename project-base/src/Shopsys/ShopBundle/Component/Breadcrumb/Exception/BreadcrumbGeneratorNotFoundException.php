<?php

namespace Shopsys\ShopBundle\Component\Breadcrumb\Exception;

use Exception;

class BreadcrumbGeneratorNotFoundException extends Exception implements BreadcrumbException
{
    /**
     * @param string $routeName
     * @param \Exception|null $previous
     */
    public function __construct($routeName, Exception $previous = null)
    {
        parent::__construct('Breadcrumb generator not found for route "' . $routeName . '"', 0, $previous);
    }
}
