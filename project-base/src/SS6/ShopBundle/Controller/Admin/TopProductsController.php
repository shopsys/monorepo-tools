<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TopProductsController extends Controller {

	/**
	 * @Route("/product/top_products_homepage/")
	 */
	public function indexAction() {
		$topProductInlineEdit = $this->get('ss6.shop.product.top_product.top_product_inline_edit');
		/* @var $topProductInlineEdit \SS6\ShopBundle\Model\Product\TopProduct\TopProductInlineEdit */

		$grid = $topProductInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/TopProducts/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/product/top_products_homepage/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$topProductFacade = $this->get('ss6.shop.product.top_product.top_product_facade');
		/* @var $topProductFacade \SS6\ShopBundle\Model\Product\TopProduct\TopProductFacade */

		try {
			$topProductFacade->getById($id);
			$topProductFacade->deleteById($id);

			$flashMessageSender->addSuccessTwig('Zboží bylo odstraněno');
		} catch (\SS6\ShopBundle\Model\Product\TopProduct\Exception\TopProductNotFoundException $e) {
			$flashMessageSender->addErrorTwig('Zboží se nepodařilo odstranit - zboží již není v tomto seznamu.');
		}

		return $this->redirect($this->generateUrl('admin_topproducts_index'));
	}
}