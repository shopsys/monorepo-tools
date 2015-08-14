<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Model\AdminNavigation\Breadcrumb;

class BreadcrumbController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	public function __construct(Breadcrumb $breadcrumb) {
		$this->breadcrumb = $breadcrumb;
	}

	public function indexAction() {
		$items = $this->breadcrumb->getItems();

		return $this->render('@SS6Shop/Admin/Inline/Breadcrumb/breadcrumb.html.twig', [
			'items' => $items,
		]);
	}

}
