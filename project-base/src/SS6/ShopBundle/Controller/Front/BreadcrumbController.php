<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Breadcrumb\BreadcrumbResolver;
use SS6\ShopBundle\Component\Controller\FrontBaseController;

class BreadcrumbController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Breadcrumb\BreadcrumbResolver
	 */
	private $breadcrumbResolver;

	public function __construct(
		BreadcrumbResolver $breadcrumbResolver
	) {
		$this->breadcrumbResolver = $breadcrumbResolver;
	}

	/**
	 * @param string $routeName
	 * @param array $routeParameters
	 */
	public function indexAction($routeName, array $routeParameters = []) {
		$breadcrumbItems = $this->breadcrumbResolver->resolveBreadcrumbItems($routeName, $routeParameters);

		return $this->render('@SS6Shop/Front/Inline/Breadcrumb/breadcrumb.html.twig', [
			'breadcrumbItems' => $breadcrumbItems,
		]);
	}

}
