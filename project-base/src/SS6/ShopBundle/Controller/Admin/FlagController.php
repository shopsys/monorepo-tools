<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FlagController extends Controller {

	/**
	 * @Route("/product/flag/list/")
	 */
	public function listAction() {
		$productInlineEdit = $this->get('ss6.shop.product.flag.flag_inline_edit');
		/* @var $productInlineEdit \SS6\ShopBundle\Model\Product\Flag\FlagInlineEdit */

		$grid = $productInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Flag/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/product/flag/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$flagFacade = $this->get('ss6.shop.product.flag.flag_facade');
		/* @var $flagFacade \SS6\ShopBundle\Model\Product\Flag\FlagFacade */

		try {
			$fullName = $flagFacade->getById($id)->getName();
			$flagFacade->deleteById($id);

			$flashMessageSender->addSuccessFlashTwig('Příznak <strong>{{ name }}</strong> byl smazán', [
				'name' => $fullName,
			]);
		} catch (\SS6\ShopBundle\Model\Product\Flag\Exception\FlagNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolený příznak neexistuje.');
		}

		return $this->redirect($this->generateUrl('admin_flag_list'));
	}

}
