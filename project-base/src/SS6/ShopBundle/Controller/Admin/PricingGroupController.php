<?php

namespace SS6\ShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory;
use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Component\Router\Security\Annotation\CsrfProtection;
use SS6\ShopBundle\Form\Admin\Pricing\Group\PricingGroupSettingsFormType;
use SS6\ShopBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PricingGroupController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade
	 */
	private $pricingGroupFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\Grid\PricingGroupInlineEdit
	 */
	private $pricingGroupInlineEdit;

	/**
	 * @var \SS6\ShopBundle\Component\ConfirmDelete\ConfirmDeleteResponseFactory
	 */
	private $confirmDeleteResponseFactory;

	public function __construct(
		PricingGroupSettingFacade $pricingGroupSettingFacade,
		PricingGroupFacade $pricingGroupFacade,
		PricingGroupInlineEdit $pricingGroupInlineEdit,
		ConfirmDeleteResponseFactory $confirmDeleteResponseFactory
	) {
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
		$this->pricingGroupFacade = $pricingGroupFacade;
		$this->pricingGroupInlineEdit = $pricingGroupInlineEdit;
		$this->confirmDeleteResponseFactory = $confirmDeleteResponseFactory;
	}

	/**
	 * @Route("/pricing/group/list/")
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function listAction() {
		$grid = $this->pricingGroupInlineEdit->getGrid();

		return $this->render('@SS6Shop/Admin/Content/Pricing/Groups/list.html.twig', [
			'gridView' => $grid->createView(),
		]);
	}

	/**
	 * @Route("/pricing/group/delete/{id}", requirements={"id" = "\d+"})
	 * @CsrfProtection
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $id
	 */
	public function deleteAction(Request $request, $id) {
		$newId = $request->get('newId');
		$newId = $newId !== null ? (int)$newId : null;

		try {
			$name = $this->pricingGroupFacade->getById($id)->getName();

			$this->pricingGroupFacade->delete($id, $newId);

			if ($newId === null) {
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Pricing group <strong>{{ name }}</strong> deleted'),
					[
						'name' => $name,
					]
				);
			} else {
				$newPricingGroup = $this->pricingGroupFacade->getById($newId);
				$this->getFlashMessageSender()->addSuccessFlashTwig(
					t('Pricing group <strong>{{ name }}</strong> deleted and replaced by group <strong>{{ newName }}</strong>.'),
					[
						'name' => $name,
						'newName' => $newPricingGroup->getName(),
					]
				);
			}
		} catch (\SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
			$this->getFlashMessageSender()->addErrorFlash(t('Selected pricing group doesn\'t exist.'));
		}

		return $this->redirectToRoute('admin_pricinggroup_list');
	}

	/**
	 * @Route("/pricing/group/delete-confirm/{id}", requirements={"id" = "\d+"})
	 * @param int $id
	 */
	public function deleteConfirmAction($id) {
		try {
			$pricingGroup = $this->pricingGroupFacade->getById($id);
			$remainingPricingGroups = $this->pricingGroupFacade->getAllExceptIdByDomainId($id, $pricingGroup->getDomainId());
			$remainingPricingGroupsList = new ObjectChoiceList($remainingPricingGroups, 'name', [], null, 'id');

			if ($this->pricingGroupSettingFacade->isPricingGroupUsed($pricingGroup)) {
				$message = t(
					'For removing pricing group "%name%" you have to choose other one to be set everywhere where the existing one is used. Which pricing group you want to set instead?',
					['%name%' => $pricingGroup->getName()]
				);

				if ($this->pricingGroupSettingFacade->isPricingGroupDefault($pricingGroup)) {
					$message = t(
						'Pricing group "%name%" set as default. For deleting it you have to choose other one to be set everywhere where the existing one is used. Which pricing group you want to set instead?',
						['%name%' => $pricingGroup->getName()]
					);
				}

				return $this->confirmDeleteResponseFactory->createSetNewAndDeleteResponse(
					$message,
					'admin_pricinggroup_delete',
					$id,
					$remainingPricingGroupsList
				);
			} else {
				$message = t(
					'Do you really want to remove pricing group "%name%" permanently? It is not used anywhere.',
					['%name%' => $pricingGroup->getName()]
				);
				return $this->confirmDeleteResponseFactory->createDeleteResponse($message, 'admin_pricinggroup_delete', $id);
			}

		} catch (\SS6\ShopBundle\Model\Pricing\Group\Exception\PricingGroupNotFoundException $ex) {
			return new Response(t('Selected pricing group doesn\'t exist.'));
		}

	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 */
	public function settingsAction(Request $request) {
		$pricingGroups = $this->pricingGroupSettingFacade->getPricingGroupsBySelectedDomainId();
		$form = $this->createForm(new PricingGroupSettingsFormType($pricingGroups));

		$pricingGroupSettingsFormData = [];
		$pricingGroupSettingsFormData['defaultPricingGroup'] = $this->pricingGroupSettingFacade
			->getDefaultPricingGroupBySelectedDomain();

		$form->setData($pricingGroupSettingsFormData);

		$form->handleRequest($request);

		if ($form->isValid()) {
			$pricingGroupSettingsFormData = $form->getData();

			$this->pricingGroupSettingFacade->setDefaultPricingGroup($pricingGroupSettingsFormData['defaultPricingGroup']);

			$this->getFlashMessageSender()->addSuccessFlash(t('Default pricing group settings modified'));

			return $this->redirectToRoute('admin_pricinggroup_list');
		}

		return $this->render('@SS6Shop/Admin/Content/Pricing/Groups/pricingGroupSettings.html.twig', [
			'form' => $form->createView(),
		]);

	}
}
