<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;

class BreadcrumbController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	public function __construct(Breadcrumb $breadcrumb) {
		$this->breadcrumb = $breadcrumb;
	}

	/**
	 * @param string $route
	 * @param array|null $parameters
	 */
	public function indexAction($route, array $parameters = null) {
		$items = $this->breadcrumb->getItems($route, $parameters);

		return $this->render('@SS6Shop/Admin/Inline/Breadcrumb/breadcrumb.html.twig', [
			'items' => $items,
		]);
	}

}
