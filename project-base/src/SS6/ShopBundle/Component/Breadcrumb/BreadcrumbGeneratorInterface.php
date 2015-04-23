<?php

namespace SS6\ShopBundle\Component\Breadcrumb;

interface BreadcrumbGeneratorInterface {

	/**
	 * @param string $routeName
	 * @param array $routeParameters
	 * @return \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
	 */
	public function getBreadcrumbItems($routeName, array $routeParameters = []);

}
