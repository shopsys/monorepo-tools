<?php

namespace Shopsys\ShopBundle\Component\Breadcrumb;

interface BreadcrumbGeneratorInterface
{

    /**
     * @param string $routeName
     * @param array $routeParameters
     * @return \Shopsys\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = []);
}
