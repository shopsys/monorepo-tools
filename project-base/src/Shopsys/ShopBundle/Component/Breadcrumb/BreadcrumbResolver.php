<?php

namespace SS6\ShopBundle\Component\Breadcrumb;

class BreadcrumbResolver {

	/**
	 * @var \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface[routeName]
	 */
	private $breadcrumbGeneratorsByRouteName;

	public function __construct() {
		$this->breadcrumbGeneratorsByRouteName = [];
	}

	/**
	 * @param string $routeName
	 * @param \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface $breadcrumbGenerator
	 */
	public function registerGenerator($routeName, BreadcrumbGeneratorInterface $breadcrumbGenerator) {
		$this->breadcrumbGeneratorsByRouteName[$routeName] = $breadcrumbGenerator;
	}

	/**
	 * @param string $routeName
	 * @param array $routeParameters
	 * @return \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbItem[]
	 */
	public function resolveBreadcrumbItems($routeName, array $routeParameters = []) {
		if (!$this->hasGeneratorForRoute($routeName)) {
			throw new \SS6\ShopBundle\Component\Breadcrumb\Exception\BreadcrumbGeneratorNotFoundException($routeName);
		}

		$breadcrumbGenerator = $this->breadcrumbGeneratorsByRouteName[$routeName];

		try {
			return $breadcrumbGenerator->getBreadcrumbItems($routeName, $routeParameters);
		} catch (\Exception $ex) {
			throw new \SS6\ShopBundle\Component\Breadcrumb\Exception\UnableToGenerateBreadcrumbItemsException($ex);
		}
	}

	/**
	 * @param string $routeName
	 * @return bool
	 */
	public function hasGeneratorForRoute($routeName) {
		return array_key_exists($routeName, $this->breadcrumbGeneratorsByRouteName);
	}

}
