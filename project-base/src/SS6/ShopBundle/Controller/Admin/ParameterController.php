<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParameterController extends Controller {

	/**
	 * @Route("/product/parameter/list/")
	 */
	public function listAction() {
		$parameterInlineEdit = $this->get('ss6.shop.product.parameter.parameter_inline_edit');
		/* @var $parameterInlineEdit \SS6\ShopBundle\Model\Product\Parameter\ParameterInlineEdit */

		$grid = $parameterInlineEdit->getGrid();
		
		return $this->render('@SS6Shop/Admin/Content/Parameter/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/product/parameter/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$parameterFacade = $this->get('ss6.shop.product.parameter.parameter_facade');
		/* @var $parameterFacade \SS6\ShopBundle\Model\Product\Parameter\ParameterFacade */

		$fullName = $parameterFacade->getById($id)->getName();
		$parameterFacade->deleteById($id);

		$flashMessageSender->addSuccessTwig('Parametr <strong>{{ name }}</strong> byla smazán', array(
			'name' => $fullName,
		));
		return $this->redirect($this->generateUrl('admin_parameter_list'));
	}

}
