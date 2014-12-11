<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Form\Admin\Pricing\Group\PricingGroupSettingsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

		$newId = $newId !== null ? (int)$newId : null;

		try {
			$name = $pricingGroupFacade->getById($id)->getName();
			$pricingGroupFacade->delete($id, $newId);

			if ($newId === null) {
				$flashMessageSender->addSuccessFlashTwig('Cenová skupina <strong>{{ name }}</strong> byla smazána', array(
					'name' => $name,
				));
			} else {
				$newPricingGroup = $pricingGroupFacade->getById($newId);
				$flashMessageSender->addSuccessFlashTwig(
					'Cenová skupina <strong>{{ name }}</strong> byla smazána a byla nahrazena skupinou'
					. ' <strong>{{ newName }}</strong>.',
					array(
						'name' => $name,
						'newName' => $newPricingGroup->getName(),
					));
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
			$flashMessageSender->addErrorFlash('Zvolená cenová skupina již neexistuje');
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
		/* @var $confirmDeleteResponseFactory \SS6\ShopBundle\Model\ConfirmDelete\ConfirmDeleteResponseFactory */

		try {
			$pricingGroup = $pricingGroupFacade->getById($id);
			$pricingGroupsNamesById = array();
			foreach ($pricingGroupFacade->getAllExceptIdByDomainId($id, $pricingGroup->getDomainId()) as $newPricingGroup) {
				$pricingGroupsNamesById[$newPricingGroup->getId()] = $newPricingGroup->getName();
			}
			if ($pricingGroupFacade->isPricingGroupUsed($pricingGroup)) {
				$message = 'Pro odstranění cenové skupiny "' . $pricingGroup->getName() . '" musíte zvolit, která se má všude, '
					. 'kde je aktuálně používaná, nastavit.' . "\n\n" . 'Jakou cenovou skupinu místo ní chcete nastavit?';

				if ($pricingGroupFacade->isPricingGroupDefault($pricingGroup)) {
					$message = 'Cenová skupina "' . $pricingGroup->getName() . '" je nastavena jako výchozí. '
						. 'Pro její odstranění musíte zvolit, která se má všude, '
						. 'kde je aktuálně používaná, nastavit.' . "\n\n" . 'Jakou cenovou skupinu místo ní chcete nastavit?';
				}

				return $confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_pricinggroup_delete',
					$id,
					$pricingGroupsNamesById
				);
			} else {
				$message = 'Opravdu si přejete trvale odstranit cenovou skupinu "' . $pricingGroup->getName() . '"?'
					. ' Nikde není použita.';
				return $confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_pricinggroup_delete', $id);
			}

		} catch (\SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
			return new Response('Zvolená cenová skupina již neexistuje');
		}

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingsAction(Request $request) {
		$pricingGroupFacade = $this->get('ss6.shop.pricing.group.pricing_group_facade');
		/* @var $pricingGroupFacade \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade */
		$flashMessageSender = $this->get('ss6.shop.flash_message.sender.admin');
		/* @var $flashMessageSender \SS6\ShopBundle\Model\FlashMessage\FlashMessageSender */

		$pricingGroups = $pricingGroupFacade->getPricingGroupsBySelectedDomainId();
		$form = $this->createForm(new PricingGroupSettingsFormType($pricingGroups));

		$pricingGroupSettingsFormData = array();
		$pricingGroupSettingsFormData['defaultPricingGroup'] =  $pricingGroupFacade->getDefaultPricingGroupBySelectedDomain();

		$form->setData($pricingGroupSettingsFormData);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$pricingGroupSettingsFormData = $form->getData();
			$pricingGroupFacade->setDefaultPricingGroup($pricingGroupSettingsFormData['defaultPricingGroup']);
			$flashMessageSender->addSuccessFlash('Nastavení výchozí cenové skupiny bylo upraveno');

			return $this->redirect($this->generateUrl('admin_pricinggroup_list'));
		}

		return $this->render('@SS6Shop/Admin/Content/Pricing/Groups/pricingGroupSettings.html.twig', array(
			'form' => $form->createView(),
		));

	}
}
