<?php

namespace Shopsys\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\ShopBundle\Component\Controller\AdminBaseController;
use Shopsys\ShopBundle\Model\Localization\Translation\Grid\TranslationInlineEdit;

class TranslationController extends AdminBaseController {

	/**
	 * @var \Shopsys\ShopBundle\Model\Localization\Translation\Grid\TranslationInlineEdit
	 */
	private $translationInlineEdit;

	public function __construct(TranslationInlineEdit $translationInlineEdit) {
		$this->translationInlineEdit = $translationInlineEdit;
	}

	/**
	 * @Route("/translation/list/")
	 */
	public function listAction() {
		$grid = $this->translationInlineEdit->getGrid();

		return $this->render('@ShopsysShop/Admin/Content/Translation/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
