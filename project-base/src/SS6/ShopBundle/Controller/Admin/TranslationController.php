<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Grid\ArrayDataSource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TranslationController extends Controller {

	/**
	 * @Route("/translation/list/")
	 */
	public function listAction() {
		$translationInlineEdit = $this->get('ss6.shop.localization.translation.grid.translation_inline_edit');
		/* @var $$translationInlineEdit \SS6\ShopBundle\Model\Localization\Translation\Grid\TranslationInlineEdit */

		$grid = $translationInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Translation/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	

}
