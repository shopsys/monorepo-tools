<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PricingGroupController extends Controller {

	/**
	 * @Route("/pricing/group/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$pricingGroupInlineEdit = $this->get('ss6.shop.pricing.group.grid.pricing_group_inline_edit');
		/* @var $pricingGroupInlineEdit \SS6\ShopBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit */

		$grid = $pricingGroupInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Pricing/Groups/list.html.twig', array(
			'gridView' => $grid->createView(),
		));
	}

	/**
	 * @Route("/pricing/group/delete/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteAction($id) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$pricingGroupFacade = $this->get('ss6.shop.pricing.group.pricing_group_facade');
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

		$name = $pricingGroupFacade->getById($id)->getName();
		$pricingGroupFacade->delete($id);

		$flashMessageSender->addSuccessTwig('Cenová skupina <strong>{{ name }}</strong> byla smazána', array(
			'name' => $name,
		));

		return $this->redirect($this->generateUrl('admin_pricinggroup_list'));
	}
}
