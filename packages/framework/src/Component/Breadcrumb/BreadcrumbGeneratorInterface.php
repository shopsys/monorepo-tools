<?php

namespace Shopsys\FrameworkBundle\Component\Breadcrumb;

interface BreadcrumbGeneratorInterface
{
    /**
     * @param string $routeName
     * @param array $routeParameters
     * @return \Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbItem[]
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = []);

    /**
     * @return string[]
     */
    public function getRouteNames();
}
