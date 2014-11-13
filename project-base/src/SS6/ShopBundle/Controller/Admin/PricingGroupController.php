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
	 * @Route("/pricing/group/delete/{id}/{newId}", requirements={"id" = "\d+", "newId" = "\d+"})
	 * @param int $id
	 * @param int|null $newId
	 */
	public function deleteAction($id, $newId = null) {
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */
		$pricingGroupFacade = $this->get('ss6.shop.pricing.group.pricing_group_facade');
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */

		$newId = (int)$newId === 0 ? null : $newId;

		$name = $pricingGroupFacade->getById($id)->getName();
		$pricingGroupFacade->delete($id, $newId);

		if ($newId === null) {
			$flashMessageSender->addSuccessTwig('Cenová skupina <strong>{{ name }}</strong> byla smazána', array(
				'name' => $name,
			));
		} else {
			$newPricingGroup = $pricingGroupFacade->getById($newId);
			$flashMessageSender->addSuccessTwig(
				'Cenová skupina <strong>{{ name }}</strong> byla smazána a byla nahrazena skupinou <strong>{{ newName }}</strong>.',
				array(
					'name' => $name,
					'newName' => $newPricingGroup->getName(),
				));
		}

		return $this->redirect($this->generateUrl('admin_pricinggroup_list'));
	}

	/**
	 * @Route("/pricing/group/delete_confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		$pricingGroupFacade = $this->get('ss6.shop.pricing.group.pricing_group_facade');
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */
		$confirmDeleteResponseFactory = $this->get('ss6.shop.confirm_delete.confirm_delete_response_factory');
		/* @var $confirmDeleteResponseFactory \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory */;

		$pricingGroup = $pricingGroupFacade->getById($id);
		if ($pricingGroupFacade->isPricingGroupUsed($pricingGroup)) {
			$message = 'Pro odstranění cenové skupiny "' . $pricingGroup->getName() . '" musíte zvolit, která se má všude, '
				. 'kde je aktuálně používaná, nastavit. Jakou cenovou skupinu místo ní chcete nastavit?';
			$pricingGroupsNamesById = [0 => '-- žádná --'];
			foreach ($pricingGroupFacade->getAllExceptIdByDomainId($id, $pricingGroup->getDomainId()) as $newPricingGroup) {
				$pricingGroupsNamesById[$newPricingGroup->getId()] = $newPricingGroup->getName();
			}
			return $confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
				$message,
				'admin_pricinggroup_delete',
				$id,
				$pricingGroupsNamesById
			);
		} else {
			$message = 'Opravdu si přejete trvale odstranit cenovou skupinu "' . $pricingGroup->getName() . '"? Nikde není použita.';
			return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_pricinggroup_delete', $id);
		}

	}
}
