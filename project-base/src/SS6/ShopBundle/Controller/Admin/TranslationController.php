<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Model\Localization\Translation\Grid\TranslationInlineEdit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TranslationController extends Controller {

	/**
	 * @var \SS6\ShopBundle\Model\Localization\Translation\Grid\TranslationInlineEdit
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

		return $this->render('@SS6Shop/Admin/Content/Translation/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

}
